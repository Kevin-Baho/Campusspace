<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityUnit;
use App\Services\QRCodeService;
use Inertia\Inertia;

class QRController extends Controller
{
    public function index(QRCodeService $qr)
    {
        $units = FacilityUnit::with('facility:id,name')->where('is_active', true)->orderBy('code')->paginate(12);
        $units->getCollection()->transform(function ($u) use ($qr) {
            $u->setAttribute('qr_svg', $qr->svg($u, 180));
            $u->setAttribute('qr_url', $qr->urlFor($u));
            return $u;
        });
        return Inertia::render('Admin/QR', ['units' => $units]);
    }

    public function regenerate(FacilityUnit $unit, QRCodeService $qr)
    {
        $qr->regenerate($unit);
        return back()->with('success', 'QR baru dibuat. QR lama tidak berlaku lagi; cetak ulang.');
    }
}
