<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'device_id',
        'latitude',
        'longitude',
        'altitude',
        'speed',
        'heading',
        'accuracy',
        'timestamp',

    ] ;

    public function gpsDevice()
    {
        return $this->belongsTo(GpsDevice::class, 'device_id', 'id');
    }
}
