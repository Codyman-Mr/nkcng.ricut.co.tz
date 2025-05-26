<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerVehicle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'imei',
        'user_id',
        'model',
        'plate_number',
        'vehicle_type',
        'fuel_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installations()
    {
        return $this->hasMany(Installation::class);
    }

    public function loans()
    {
        return $this->hasManyThrough(Loan::class, Installation::class, 'customer_vehicle_id', 'installation_id');
    }

    public function gpsRecords()
    {
        return $this->hasOne(CustomerVehicleGps::class, 'imei', 'imei');
    }

    public function gpsDevice()
    {
        return $this->hasOne(GpsDevice::class, 'vehicle_id');
    }
}


