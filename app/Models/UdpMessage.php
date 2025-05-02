<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UdpMessage extends Model
{
    protected $fillable = ['ip', 'port', 'latitude', 'longitude'];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}
