<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingReview;
use App\Services\BookingService;
use Illuminate\Support\Facades\Http;

class ReviewAndAiTest extends CampusSpaceTestCase
{
    public function test_review_only_for_completed_and_once(): void
    {
        $u = $this->user(); $unit = $this->unit();
        $b = app(BookingService::class)->create($u, $unit->id, '11:00', '12:00');
        $this->actingAs($u)->post("/bookings/{$b->id}/review", ['rating' => 5])->assertSessionHasErrors('rating');

        $b->update(['status' => 'completed']);
        $this->actingAs($u)->post("/bookings/{$b->id}/review", ['rating' => 5, 'comment' => 'ok'])->assertSessionHasNoErrors();
        $this->actingAs($u)->post("/bookings/{$b->id}/review", ['rating' => 4])->assertSessionHasErrors('rating');
        $this->assertSame(1, BookingReview::count());
    }

    public function test_review_validation_and_ownership(): void
    {
        $u = $this->user(); $unit = $this->unit();
        $b = app(BookingService::class)->create($u, $unit->id, '11:00', '12:00');
        $b->update(['status' => 'completed']);
        $this->actingAs($u)->post("/bookings/{$b->id}/review", ['rating' => 9])->assertSessionHasErrors('rating');
        $this->actingAs($this->user())->post("/bookings/{$b->id}/review", ['rating' => 5])->assertForbidden();
    }

    public function test_ai_prompt_uses_database_facilities_and_cannot_mutate_bookings(): void
    {
        config(['services.groq.key' => 'test-key', 'services.groq.base_url' => 'https://api.groq.test/v1', 'services.groq.model' => 'm']);
        $unit = $this->unit('ROOM-XYZ', 'room', 12);
        $u = $this->user();
        Http::fake(['api.groq.test/*' => Http::response(['choices' => [['message' => ['content' => 'Coba ROOM-XYZ.']]]])]);

        $this->actingAs($u)->post('/assistant', ['message' => 'Cancel booking saya dan pesan ruangan 12 orang'])->assertSessionHasNoErrors();

        Http::assertSent(function ($req) {
            $sys = $req['messages'][0]['content'];
            return str_contains($sys, 'ROOM-XYZ') && str_contains($sys, 'kapasitas 12') && str_contains($sys, 'TIDAK dapat membuat');
        });
        $this->assertSame(0, Booking::count());                       // AI tidak membuat booking
        $this->assertDatabaseHas('ai_messages', ['role' => 'assistant', 'content' => 'Coba ROOM-XYZ.']);
    }

    public function test_ai_failure_is_graceful_and_does_not_leak_key(): void
    {
        config(['services.groq.key' => 'SECRET-KEY-123', 'services.groq.base_url' => 'https://api.groq.test/v1']);
        Http::fake(['api.groq.test/*' => Http::response('boom', 500)]);
        $u = $this->user();
        $this->actingAs($u)->post('/assistant', ['message' => 'halo'])->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('ai_messages', ['content' => 'SECRET-KEY-123']);
        $this->assertDatabaseHas('ai_messages', ['role' => 'assistant']);
    }

    public function test_ai_requires_auth(): void
    {
        $this->post('/assistant', ['message' => 'x'])->assertRedirect('/login');
    }
}
