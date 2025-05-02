<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerVehicle extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installations()
    {
        return $this->hasMany(Installation::class);
    }

    public function gpsRecords()
    {
        return $this->hasOne(CustomerVehicleGps::class, 'imei', 'imei');
    }

    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'model',
        'plate_number',
        'vehicle_type',
        'fuel_type',
    ];
}



