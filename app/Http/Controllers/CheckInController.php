<?php

namespace App\Http\Controllers;

use App\Services\CheckInService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckInController extends Controller
{
    public function index()
    {
        return Inertia::render('CheckIn/Scan', ['token' => null]);
    }

    // Dibuka saat QR dipindai memakai kamera bawaan HP; user harus login.
    public function scan(string $token)
    {
        return Inertia::render('CheckIn/Scan', ['token' => $token]);
    }

    public function store(Request $request, CheckInService $svc)
    {
        $data = $request->validate(['token' => ['required', 'string', 'max:64']]);
        $b = $svc->checkIn($request->user(), $data['token'], $request->ip(), $request->userAgent());
        return redirect()->route('bookings.show', $b)->with('success', 'Check-in berhasil.');
    }
}
