<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class GpsDevice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'device_id',
        'vehicle_id',
        'activity_status',
        'assignment_status',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'unassigned_at',
        'unassigned_by',
        'unassigned_reason',
        'power_status',
        'power_status_updated_at',
        'power_status_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'unassigned_at' => 'datetime',
        'power_status_updated_at' => 'datetime',
    ];

    protected $table = 'gps_devices';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'device_id', 'id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function customerVehicle()
    {
        return $this->belongsTo(CustomerVehicle::class, 'vehicle_id');
    }

    public function gpsDevice()
    {
        return $this->hasOne(GpsDevice::class, 'device_id', 'device_id');
    }

    public function getRouteKey()
    {
        return 'device_id';
    }
}
