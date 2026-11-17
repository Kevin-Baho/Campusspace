<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FacilityUnit;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private AvailabilityService $availability,
        private NotificationService $notifications,
    ) {}

    /**
     * Buat booking. Status ditentukan di sini (bukan dari request/AI).
     * Locking: baris user dikunci (cegah 2 booking aktif paralel dari akun sama) lalu baris unit
     * dikunci (cegah bentrok dari dua pengguna). Di SQLite (test) lockForUpdate diabaikan.
     */
    public function create(User $user, int $unitId, string $startTime, string $endTime, ?string $purpose = null): Booking
    {
        $tz = $this->availability->tz();
        $now = $this->availability->now();
        $date = $now->toDateString(); // hanya hari ini (zona waktu kampus)

        $start = Carbon::parse("$date $startTime", $tz);
        $end = Carbon::parse("$date $endTime", $tz);

        if ($end->lte($start)) $this->fail('end_time', 'Jam selesai harus setelah jam mulai.');
        if ($start->lt($now->copy()->startOfMinute())) $this->fail('start_time', 'Jam mulai tidak boleh sudah lewat.');

        $minutes = $start->diffInMinutes($end);
        $min = SystemSetting::get('min_duration_minutes');
        $max = SystemSetting::get('max_duration_minutes');
        if ($min !== null && $minutes < (int) $min) $this->fail('end_time', "Durasi minimal {$min} menit.");
        if ($max !== null && $minutes > (int) $max) $this->fail('end_time', "Durasi maksimal {$max} menit.");

        return DB::transaction(function () use ($user, $unitId, $start, $end, $purpose, $date) {
            User::whereKey($user->id)->lockForUpdate()->first();

            if (Booking::where('user_id', $user->id)->active()->exists()) {
                $this->fail('facility_unit_id', 'Anda sudah memiliki booking aktif. Selesaikan atau batalkan terlebih dahulu.');
            }

            $unit = FacilityUnit::whereKey($unitId)->lockForUpdate()->first();
            if (!$unit || !$unit->is_active || $unit->status !== 'available') {
                $this->fail('facility_unit_id', 'Unit tidak tersedia.');
            }
            if (!$this->availability->isFree($unit->id, $start, $end)) {
                $this->fail('start_time', 'Unit sudah dipesan pada rentang waktu tersebut.');
            }

            $booking = Booking::create([
                'user_id' => $user->id,
                'facility_unit_id' => $unit->id,
                'booking_date' => $date,
                'starts_at' => $start,
                'ends_at' => $end,
                'purpose' => $purpose,
                'status' => 'confirmed',
                'checkin_deadline_at' => $start->copy()->addMinutes($this->availability->graceMinutes()),
            ]);

            $label = $unit->code;
            $this->notifications->forBooking($booking, 'booking_created', 'Booking berhasil dibuat',
                "Unit {$label}, {$start->format('H:i')}–{$end->format('H:i')}. Check-in paling lambat {$booking->checkin_deadline_at->format('H:i')}.");
            $this->notifications->forBooking($booking, 'booking_confirmed', 'Booking dikonfirmasi', "Unit {$label} telah dikonfirmasi.");

            return $booking;
        });
    }

    public function cancel(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $b = Booking::whereKey($booking->id)->lockForUpdate()->first();
            // Hanya booking yang belum check-in yang bisa dibatalkan pengguna.
            if (!in_array($b->status, ['pending', 'confirmed'], true)) {
                $this->fail('booking', 'Booking ini tidak dapat dibatalkan.');
            }
            $b->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            $this->notifications->forBooking($b, 'booking_cancelled', 'Booking dibatalkan', 'Booking Anda telah dibatalkan.');
            return $b;
        });
    }

    /** Dipanggil scheduler: expire yang lewat batas check-in, selesaikan yang lewat jam selesai, kirim pengingat. */
    public function processDue(): array
    {
        $now = $this->availability->now();
        $expired = $completed = $reminded = 0;

        Booking::where('status', 'confirmed')->where('checkin_deadline_at', '<', $now)->get()
            ->each(function (Booking $b) use (&$expired) {
                $updated = Booking::whereKey($b->id)->where('status', 'confirmed')->update(['status' => 'expired']);
                if ($updated) {
                    $expired++;
                    $this->notifications->forBooking($b, 'booking_expired', 'Booking kedaluwarsa', 'Anda tidak check-in sebelum batas waktu, booking dibatalkan otomatis.');
                }
            });

        Booking::where('status', 'checked_in')->where('ends_at', '<=', $now)->get()
            ->each(function (Booking $b) use (&$completed) {
                $updated = Booking::whereKey($b->id)->where('status', 'checked_in')->update(['status' => 'completed']);
                if ($updated) {
                    $completed++;
                    $this->notifications->forBooking($b, 'booking_completed', 'Booking selesai', 'Terima kasih. Anda dapat memberi ulasan fasilitas.');
                }
            });

        // Pengingat: booking confirmed yang mulai dalam <= 5 menit, sekali saja.
        Booking::where('status', 'confirmed')->whereNull('reminder_sent_at')
            ->where('starts_at', '<=', $now->copy()->addMinutes(5))->where('starts_at', '>=', $now->copy()->subMinutes(1))
            ->get()->each(function (Booking $b) use (&$reminded) {
                $b->update(['reminder_sent_at' => now()]);
                $reminded++;
                $this->notifications->forBooking($b, 'checkin_reminder', 'Pengingat check-in', "Segera scan QR di unit {$b->unit->code}. Batas check-in {$b->checkin_deadline_at->format('H:i')}.");
            });

        return compact('expired', 'completed', 'reminded');
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
