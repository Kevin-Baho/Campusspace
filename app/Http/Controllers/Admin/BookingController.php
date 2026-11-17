<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Bookings', [
            'bookings' => Booking::with('user:id,name,email', 'unit:id,code')
                ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
                ->latest('starts_at')->paginate(25)->withQueryString(),
            'status' => $request->string('status')->toString(),
        ]);
    }

    public function cancel(Booking $booking, BookingService $svc)
    {
        $svc->cancel($booking);
        return back()->with('success', 'Booking dibatalkan.');
    }
}
