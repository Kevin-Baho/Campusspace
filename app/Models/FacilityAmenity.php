<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityAmenity extends Model
{
    protected $fillable = ['facility_id', 'name', 'icon'];

    public function facility() { return $this->belongsTo(Facility::class); }
}
