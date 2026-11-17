<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);
        $from = $data['from'] ?? now()->subDays(29)->toDateString();
        $to = $data['to'] ?? now()->toDateString();

        $perUnit = Booking::join('facility_units', 'facility_units.id', '=', 'bookings.facility_unit_id')
            ->whereBetween('booking_date', [$from, $to])
            ->select('facility_units.code', DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN bookings.status IN ('checked_in','completed') THEN 1 ELSE 0 END) as attended"),
                DB::raw("SUM(CASE WHEN bookings.status = 'expired' THEN 1 ELSE 0 END) as expired"))
            ->groupBy('facility_units.code')->orderByDesc('total')->limit(50)->get();

        return Inertia::render('Admin/Reports', ['perUnit' => $perUnit, 'from' => $from, 'to' => $to]);
    }
}
