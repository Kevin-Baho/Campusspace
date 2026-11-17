<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public const ACTIVE = ['pending', 'confirmed', 'checked_in'];

    // status & waktu hanya diset oleh service backend, bukan dari request.
    protected $guarded = ['id'];
    protected $casts = [
        'booking_date' => 'date', 'starts_at' => 'datetime', 'ends_at' => 'datetime',
        'checkin_deadline_at' => 'datetime', 'reminder_sent_at' => 'datetime', 'cancelled_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function unit() { return $this->belongsTo(FacilityUnit::class, 'facility_unit_id'); }
    public function checkIn() { return $this->hasOne(CheckIn::class); }
    public function review() { return $this->hasOne(BookingReview::class); }

    public function scopeActive($q) { return $q->whereIn('status', self::ACTIVE); }
}
