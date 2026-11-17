<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $b): bool { return $user->id === $b->user_id || $user->isOperator(); }
    public function cancel(User $user, Booking $b): bool { return $user->id === $b->user_id || $user->isAdmin(); }
    public function review(User $user, Booking $b): bool { return $user->id === $b->user_id; }
}
