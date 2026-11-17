<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FacilityUnit;
use App\Models\SystemSetting;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $q = Booking::with('unit.facility', 'review')->where('user_id', $request->user()->id);
        return Inertia::render('Bookings/Index', [
            'mode' => 'active',
            'bookings' => (clone $q)->active()->latest('starts_at')->get(),
        ]);
    }

    public function history(Request $request)
    {
        return Inertia::render('Bookings/Index', [
            'mode' => 'history',
            'bookings' => Booking::with('unit.facility', 'review')->where('user_id', $request->user()->id)
                ->latest('starts_at')->paginate(15)->withQueryString(),
        ]);
    }

    public function create(Request $request, AvailabilityService $availability)
    {
        $unit = FacilityUnit::with('facility.amenities')->where('is_active', true)->findOrFail($request->integer('unit'));
        return Inertia::render('Bookings/Create', [
            'unit' => $unit,
            'now' => $availability->now()->format('H:i'),
            'today' => $availability->now()->toDateString(),
            'rules' => [
                'min' => SystemSetting::get('min_duration_minutes'), 'max' => SystemSetting::get('max_duration_minutes'),
                'grace' => $availability->graceMinutes(),
            ],
            'taken' => Booking::active()->where('facility_unit_id', $unit->id)
                ->whereDate('booking_date', $availability->now()->toDateString())
                ->orderBy('starts_at')->get(['starts_at', 'ends_at']),
        ]);
    }

    public function store(Request $request, BookingService $svc)
    {
        $data = $request->validate([
            'facility_unit_id' => ['required', 'integer', 'exists:facility_units,id'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'purpose' => ['nullable', 'string', 'max:255'],
        ]);
        $b = $svc->create($request->user(), $data['facility_unit_id'], $data['start_time'], $data['end_time'], $data['purpose'] ?? null);
        return redirect()->route('bookings.show', $b)->with('success', 'Booking berhasil dibuat.');
    }

    public function show(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);
        return Inertia::render('Bookings/Show', [
            'booking' => $booking->load('unit.facility', 'checkIn', 'review'),
            'canCancel' => in_array($booking->status, ['pending', 'confirmed'], true) && $request->user()->can('cancel', $booking),
        ]);
    }

    public function cancel(Booking $booking, BookingService $svc)
    {
        $this->authorize('cancel', $booking);
        $svc->cancel($booking);
        return back()->with('success', 'Booking dibatalkan.');
    }
}
