<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityUnit extends Model
{
    protected $fillable = ['facility_id', 'code', 'name', 'capacity', 'floor', 'location', 'status', 'view_images', 'is_active'];
    // qr_token sengaja tidak fillable & disembunyikan dari serialisasi publik.
    protected $hidden = ['qr_token'];
    protected $casts = ['view_images' => 'array', 'is_active' => 'boolean'];

    public function facility() { return $this->belongsTo(Facility::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
}
