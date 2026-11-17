<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingReview extends Model
{
    protected $fillable = ['booking_id', 'user_id', 'facility_id', 'rating', 'comment'];

    public function user() { return $this->belongsTo(User::class); }
    public function booking() { return $this->belongsTo(Booking::class); }
    public function facility() { return $this->belongsTo(Facility::class); }
}
