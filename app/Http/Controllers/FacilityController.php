<?php

namespace App\Http\Controllers;

use App\Models\BookingReview;
use App\Models\FacilityAmenity;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacilityController extends Controller
{
    public function index(Request $request, string $category, AvailabilityService $availability)
    {
        abort_unless(in_array($category, ['library', 'saung', 'room'], true), 404);
        $data = $request->validate([
            'start_time' => ['nullable', 'date_format:H:i'], 'end_time' => ['nullable', 'date_format:H:i'],
            'min_capacity' => ['nullable', 'integer', 'min:1'], 'amenity' => ['nullable', 'string', 'max:100'],
        ]);

        $start = $end = null;
        if (!empty($data['start_time']) && !empty($data['end_time'])) {
            $d = $availability->now()->toDateString();
            $start = Carbon::parse("$d {$data['start_time']}", $availability->tz());
            $end = Carbon::parse("$d {$data['end_time']}", $availability->tz());
            if ($end->lte($start)) $start = $end = null;
        }

        $units = $availability->unitsWithStatus($category, $start, $end, $data);
        $ratings = BookingReview::selectRaw('facility_id, AVG(rating) as avg_rating, COUNT(*) as total')
            ->whereIn('facility_id', $units->pluck('facility_id')->unique())->groupBy('facility_id')->get()->keyBy('facility_id');

        return Inertia::render('Facilities/Index', [
            'category' => $category,
            'units' => $units->values(),
            'ratings' => $ratings,
            'amenities' => FacilityAmenity::query()->distinct()->orderBy('name')->pluck('name'),
            'filters' => $data,
        ]);
    }
}
