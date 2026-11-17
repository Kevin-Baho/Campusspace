<?php

namespace App\Services;

use App\Models\FacilityUnit;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class QRCodeService
{
    public function newToken(): string
    {
        do { $t = Str::random(48); } while (FacilityUnit::where('qr_token', $t)->exists());
        return $t;
    }

    public function regenerate(FacilityUnit $unit): FacilityUnit
    {
        $unit->forceFill(['qr_token' => $this->newToken()])->save();
        return $unit;
    }

    /** URL yang dikodekan di QR. Token acak, bukan ID berurutan. */
    public function urlFor(FacilityUnit $unit): string
    {
        return url('/check-in/scan/' . $unit->qr_token);
    }

    public function svg(FacilityUnit $unit, int $size = 240): string
    {
        return (string) QrCode::format('svg')->size($size)->margin(1)->generate($this->urlFor($unit));
    }
}
