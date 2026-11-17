<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;

class CheckInTest extends CampusSpaceTestCase
{
    private function book($u, $unit, $s = '10:00', $e = '11:00'): Booking
    {
        return app(BookingService::class)->create($u, $unit->id, $s, $e);
    }

    public function test_checkin_success_within_window(): void
    {
        $u = $this->user(); $unit = $this->unit(); $b = $this->book($u, $unit);
        Carbon::setTestNow(Carbon::parse('2026-11-16 10:05:00'));
        $this->actingAs($u)->post('/check-in', ['token' => $unit->qr_token])->assertSessionHasNoErrors();
        $this->assertSame('checked_in', $b->fresh()->status);
        $this->assertNotNull($b->checkIn);
    }

    public function test_checkin_after_10_minutes_fails_and_expires(): void
    {
        $u = $this->user(); $unit = $this->unit(); $b = $this->book($u, $unit);
        Carbon::setTestNow(Carbon::parse('2026-11-16 10:11:00'));
        $this->actingAs($u)->post('/check-in', ['token' => $unit->qr_token])->assertSessionHasErrors('token');
        $this->assertSame('expired', $b->fresh()->status);
    }

    public function test_qr_cannot_checkin_other_users_booking_or_create_booking(): void
    {
        $owner = $this->user(); $intruder = $this->user(); $unit = $this->unit();
        $b = $this->book($owner, $unit);
        $this->actingAs($intruder)->post('/check-in', ['token' => $unit->qr_token])->assertSessionHasErrors('token');
        $this->assertSame('confirmed', $b->fresh()->status);
        $this->assertSame(1, Booking::count());
    }

    public function test_unknown_token_rejected(): void
    {
        $this->actingAs($this->user())->post('/check-in', ['token' => 'nope'])->assertSessionHasErrors('token');
    }

    public function test_wrong_unit_qr_rejected(): void
    {
        $u = $this->user(); $a = $this->unit('A'); $b2 = $this->unit('B');
        $this->book($u, $a);
        $this->actingAs($u)->post('/check-in', ['token' => $b2->qr_token])->assertSessionHasErrors('token');
    }

    public function test_scheduler_expires_late_bookings_and_completes_finished(): void
    {
        $late = $this->user(); $ok = $this->user();
        $u1 = $this->unit('A'); $u2 = $this->unit('B');
        $b1 = $this->book($late, $u1);
        $b2 = $this->book($ok, $u2);
        Carbon::setTestNow(Carbon::parse('2026-11-16 10:05:00'));
        $this->actingAs($ok)->post('/check-in', ['token' => $u2->qr_token]);

        Carbon::setTestNow(Carbon::parse('2026-11-16 11:30:00'));
        $this->artisan('bookings:process')->assertSuccessful();
        $this->assertSame('expired', $b1->fresh()->status);
        $this->assertSame('completed', $b2->fresh()->status);
        $this->assertDatabaseHas('campus_notifications', ['user_id' => $late->id, 'type' => 'booking_expired']);
    }
}
