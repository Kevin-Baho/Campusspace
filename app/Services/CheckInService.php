<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CheckIn;
use App\Models\FacilityUnit;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckInService
{
    public function __construct(private AvailabilityService $availability, private NotificationService $notifications) {}

    /** QR hanya memverifikasi booking milik pengguna yang login; tidak pernah membuat booking. */
    public function checkIn(User $user, string $token, ?string $ip = null, ?string $ua = null): Booking
    {
        $unit = FacilityUnit::where('qr_token', $token)->first();
        if (!$unit) $this->fail('QR Code tidak dikenali.');

        $now = $this->availability->now();
        $openBefore = (int) SystemSetting::get('checkin_open_before_minutes', 0);

        return DB::transaction(function () use ($user, $unit, $now, $openBefore, $ip, $ua) {
            $booking = Booking::where('user_id', $user->id)
                ->where('facility_unit_id', $unit->id)
                ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->whereDate('booking_date', $now->toDateString())
                ->lockForUpdate()->orderBy('starts_at')->first();

            if (!$booking) $this->fail('Anda tidak memiliki booking aktif untuk unit ini.');
            if ($booking->status === 'checked_in') $this->fail('Anda sudah check-in untuk booking ini.');
            if ($now->lt($booking->starts_at->copy()->subMinutes($openBefore))) $this->fail('Belum waktunya check-in.');
            if ($now->gt($booking->checkin_deadline_at)) {
                $booking->update(['status' => 'expired']);
                $this->notifications->forBooking($booking, 'booking_expired', 'Booking kedaluwarsa', 'Check-in melewati batas waktu.');
                $this->fail('Batas waktu check-in telah lewat. Booking kedaluwarsa.');
            }
            if ($now->gte($booking->ends_at)) $this->fail('Waktu booking telah berakhir.');

            CheckIn::create([
                'booking_id' => $booking->id, 'user_id' => $user->id, 'facility_unit_id' => $unit->id,
                'checked_in_at' => $now, 'ip_address' => $ip, 'user_agent' => $ua ? mb_substr($ua, 0, 255) : null,
            ]);
            $booking->update(['status' => 'checked_in']);
            return $booking->fresh('unit');
        });
    }

    private function fail(string $m): never
    {
        throw ValidationException::withMessages(['token' => $m]);
    }
}
