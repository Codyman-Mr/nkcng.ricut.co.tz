<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GpsDevice extends Model
{
    // GpsDevice.php
    protected $fillable = ['device_id', 'name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'device_id', 'device_id');
    }
}
