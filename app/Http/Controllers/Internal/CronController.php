<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;

class CronController extends Controller
{
    public function __invoke(Request $request, BookingService $svc)
    {
        $secret = (string) config('services.cron.secret');
        $given = (string) $request->bearerToken();
        abort_unless($secret !== '' && hash_equals($secret, $given), 403);
        return response()->json($svc->processDue());
    }
}
