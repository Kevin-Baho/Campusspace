<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\SystemSetting;
use Carbon\Carbon;

class BookingTest extends CampusSpaceTestCase
{
    private function post_booking($user, $unit, $s = '11:00', $e = '12:00')
    {
        return $this->actingAs($user)->post('/bookings', ['facility_unit_id' => $unit->id, 'start_time' => $s, 'end_time' => $e]);
    }

    public function test_booking_success(): void
    {
        $u = $this->user(); $unit = $this->unit();
        $this->post_booking($u, $unit)->assertRedirect();
        $b = Booking::first();
        $this->assertSame('confirmed', $b->status);
        $this->assertSame('2026-11-16', $b->booking_date->toDateString());
        $this->assertSame('11:10', $b->checkin_deadline_at->format('H:i'));
    }

    public function test_user_cannot_have_two_active_bookings(): void
    {
        $u = $this->user(); $a = $this->unit('A'); $b = $this->unit('B');
        $this->post_booking($u, $a);
        $this->post_booking($u, $b, '13:00', '14:00')->assertSessionHasErrors('facility_unit_id');
        $this->assertSame(1, Booking::count());
    }

    public function test_overlapping_booking_is_rejected(): void
    {
        $unit = $this->unit();
        $this->post_booking($this->user(), $unit, '11:00', '12:00');
        $this->post_booking($this->user(), $unit, '11:30', '12:30')->assertSessionHasErrors('start_time');
        $this->post_booking($this->user(), $unit, '12:00', '13:00')->assertSessionHasNoErrors(); // menempel = boleh
        $this->assertSame(2, Booking::count());
    }

    public function test_past_start_and_invalid_range_rejected(): void
    {
        $unit = $this->unit();
        $this->post_booking($this->user(), $unit, '09:00', '09:30')->assertSessionHasErrors('start_time');
        $this->post_booking($this->user(), $unit, '12:00', '11:00')->assertSessionHasErrors('end_time');
    }

    public function test_only_today_cannot_be_overridden_by_client(): void
    {
        $u = $this->user(); $unit = $this->unit();
        $this->actingAs($u)->post('/bookings', ['facility_unit_id' => $unit->id, 'start_time' => '11:00', 'end_time' => '12:00', 'booking_date' => '2026-11-20', 'status' => 'completed']);
        $b = Booking::first();
        $this->assertSame('2026-11-16', $b->booking_date->toDateString());
        $this->assertSame('confirmed', $b->status);
    }

    public function test_admin_configured_duration_limit_is_enforced(): void
    {
        SystemSetting::put('max_duration_minutes', 60);
        $this->post_booking($this->user(), $this->unit(), '11:00', '13:00')->assertSessionHasErrors('end_time');
    }

    public function test_cannot_view_or_cancel_other_users_booking(): void
    {
        $unit = $this->unit();
        $this->post_booking($this->user(), $unit);
        $b = Booking::first();
        $other = $this->user();
        $this->actingAs($other)->get("/bookings/{$b->id}")->assertForbidden();
        $this->actingAs($other)->post("/bookings/{$b->id}/cancel")->assertForbidden();
    }

    public function test_owner_can_cancel(): void
    {
        $u = $this->user();
        $this->post_booking($u, $this->unit());
        $b = Booking::first();
        $this->actingAs($u)->post("/bookings/{$b->id}/cancel")->assertSessionHasNoErrors();
        $this->assertSame('cancelled', $b->fresh()->status);
    }
}
