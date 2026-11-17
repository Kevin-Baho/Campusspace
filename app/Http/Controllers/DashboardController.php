<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request, AvailabilityService $availability)
    {
        $uid = $request->user()->id;
        return Inertia::render('Dashboard/Index', [
            'active' => Booking::with('unit.facility')->where('user_id', $uid)->active()->first(),
            'recent' => Booking::with('unit.facility')->where('user_id', $uid)->latest('starts_at')->limit(5)->get(),
            'availability' => $availability->summaryByCategory(),
            'notifications' => Notification::where('user_id', $uid)->latest()->limit(5)->get(),
        ]);
    }
}
