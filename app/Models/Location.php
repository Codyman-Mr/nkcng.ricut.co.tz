<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    public function gpsDevice()
    {
        return $this->hasOne(GpsDevice::class, 'device_id', 'id');
    }
}