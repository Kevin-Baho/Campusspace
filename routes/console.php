<?php

use Illuminate\Support\Facades\Schedule;

// Dipanggil tiap menit oleh `php artisan schedule:run` (cron / scheduler eksternal).
Schedule::command('bookings:process')->everyMinute()->withoutOverlapping();
