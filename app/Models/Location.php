<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['device_id', 'latitude', 'longitude', 'timestamp'];
    public function gpsDevice()
    {
        return $this->belongsTo(GpsDevice::class, 'device_id', 'device_id');
    }
}
