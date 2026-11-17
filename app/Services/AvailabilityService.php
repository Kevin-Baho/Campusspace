<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityUnit;
use App\Models\SystemSetting;
use Carbon\Carbon;

class AvailabilityService
{
    public function tz(): string
    {
        // Zona waktu kampus = APP_TIMEZONE (konsisten dengan penyimpanan datetime Laravel).
        return config('app.timezone', 'UTC');
    }

    public function now(): Carbon
    {
        return Carbon::now($this->tz());
    }

    public function graceMinutes(): int
    {
        return (int) SystemSetting::get('checkin_grace_minutes', 10);
    }

    /** Apakah unit bebas bentrok pada rentang waktu (batas menempel dianggap tidak bentrok). */
    public function isFree(int $unitId, Carbon $start, Carbon $end, ?int $ignoreBookingId = null): bool
    {
        return !Booking::query()
            ->where('facility_unit_id', $unitId)
            ->active()
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->exists();
    }

    /** Unit yang sedang terisi sekarang (berdasarkan booking aktif yang mencakup waktu kini). */
    public function busyUnitIdsNow(array $unitIds = []): array
    {
        $now = $this->now();
        return Booking::query()->active()
            ->when($unitIds, fn ($q) => $q->whereIn('facility_unit_id', $unitIds))
            ->where('starts_at', '<=', $now)->where('ends_at', '>', $now)
            ->pluck('facility_unit_id')->all();
    }

    /** Daftar unit sebuah kategori + status ketersediaan pada rentang tertentu (opsional). */
    public function unitsWithStatus(string $category, ?Carbon $start = null, ?Carbon $end = null, array $filters = [])
    {
        $units = FacilityUnit::query()
            ->with('facility.amenities')
            ->where('is_active', true)
            ->whereHas('facility', fn ($q) => $q->where('category', $category)->where('is_active', true))
            ->when($filters['min_capacity'] ?? null, fn ($q, $c) => $q->where('capacity', '>=', (int) $c))
            ->when($filters['amenity'] ?? null, fn ($q, $a) => $q->whereHas('facility.amenities', fn ($x) => $x->where('name', $a)))
            ->orderBy('code')->get();

        $busy = $start && $end
            ? Booking::query()->active()->whereIn('facility_unit_id', $units->pluck('id'))
                ->where('starts_at', '<', $end)->where('ends_at', '>', $start)->pluck('facility_unit_id')->all()
            : $this->busyUnitIdsNow($units->pluck('id')->all());

        return $units->map(function (FacilityUnit $u) use ($busy) {
            $u->setAttribute('available', $u->status === 'available' && !in_array($u->id, $busy, true));
            return $u;
        });
    }

    public function summaryByCategory(): array
    {
        $out = [];
        foreach (['library', 'saung', 'room'] as $cat) {
            $units = $this->unitsWithStatus($cat);
            $out[$cat] = ['total' => $units->count(), 'available' => $units->where('available', true)->count()];
        }
        return $out;
    }
}
