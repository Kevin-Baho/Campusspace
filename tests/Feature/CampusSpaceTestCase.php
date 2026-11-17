<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\FacilityUnit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class CampusSpaceTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-11-16 10:00:00', config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function user(string $role = 'student'): User
    {
        $u = User::factory()->create();
        $u->forceFill(['role' => $role])->save();
        return $u;
    }

    protected function unit(string $code = 'R-1', string $category = 'room', int $cap = 6): FacilityUnit
    {
        $f = Facility::create(['category' => $category, 'name' => "Fac $code", 'is_active' => true]);
        $u = new FacilityUnit(['facility_id' => $f->id, 'code' => $code, 'capacity' => $cap, 'status' => 'available', 'is_active' => true]);
        $u->forceFill(['qr_token' => str_repeat('a', 40) . $code])->save();
        return $u;
    }
}
