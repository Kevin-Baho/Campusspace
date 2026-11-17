<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = ['category', 'name', 'description', 'location', 'floor', 'capacity', 'image_url', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function units() { return $this->hasMany(FacilityUnit::class); }
    public function amenities() { return $this->hasMany(FacilityAmenity::class); }
    public function reviews() { return $this->hasMany(BookingReview::class); }
}
