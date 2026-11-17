<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class ProcessBookings extends Command
{
    protected $signature = 'bookings:process';
    protected $description = 'Expire booking yang lewat batas check-in, selesaikan booking berakhir, kirim pengingat.';

    public function handle(BookingService $svc): int
    {
        $r = $svc->processDue();
        $this->info("expired={$r['expired']} completed={$r['completed']} reminded={$r['reminded']}");
        return self::SUCCESS;
    }
}
