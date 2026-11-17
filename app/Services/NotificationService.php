<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notification;

class NotificationService
{
    public function send(int $userId, string $type, string $title, ?string $message = null, ?int $bookingId = null): Notification
    {
        return Notification::create([
            'user_id' => $userId, 'type' => $type, 'title' => $title,
            'message' => $message, 'booking_id' => $bookingId,
        ]);
    }

    public function forBooking(Booking $b, string $type, string $title, ?string $message = null): Notification
    {
        return $this->send($b->user_id, $type, $title, $message, $b->id);
    }
}
