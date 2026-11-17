<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['checked_in_at' => 'datetime'];

    public function booking() { return $this->belongsTo(Booking::class); }
}
