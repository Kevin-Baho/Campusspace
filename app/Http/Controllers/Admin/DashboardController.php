<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityUnit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $trend = Booking::selectRaw('DATE(booking_date) as d, COUNT(*) as total')
            ->where('booking_date', '>=', now()->subDays(13)->toDateString())
            ->groupBy('d')->orderBy('d')->get();

        $usage = Booking::join('facility_units', 'facility_units.id', '=', 'bookings.facility_unit_id')
            ->join('facilities', 'facilities.id', '=', 'facility_units.facility_id')
            ->whereIn('bookings.status', ['checked_in', 'completed'])
            ->select('facilities.category', DB::raw('COUNT(*) as total'))->groupBy('facilities.category')->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => User::count(), 'facilities' => Facility::count(), 'units' => FacilityUnit::count(),
                'bookings' => Booking::count(),
                'active' => Booking::active()->count(),
                'completed' => Booking::where('status', 'completed')->count(),
            ],
            'trend' => $trend, 'usage' => $usage,
        ]);
    }
}
