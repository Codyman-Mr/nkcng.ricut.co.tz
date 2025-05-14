<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property int $device_id
 * @property int|null $vehicle_id
 * @property string $activity_status
 * @property string $assignment_status
 * @property int|null $assigned_to
 * @property string|null $assigned_by
 * @property \Illuminate\Support\Carbon|null $assigned_at
 * @property \Illuminate\Support\Carbon|null $unassigned_at
 * @property string|null $unassigned_by
 * @property string|null $unassigned_reason
 * @property string $power_status
 * @property \Illuminate\Support\Carbon|null $power_status_updated_at
 * @property string|null $power_status_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\GpsDeviceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereActivityStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereAssignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereAssignmentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice wherePowerStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice wherePowerStatusNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice wherePowerStatusUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereUnassignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereUnassignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereUnassignedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GpsDevice withoutTrashed()
 * @mixin \Eloquent
 */
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
        return $this->belongsTo(Location::class, 'device_id', 'id');
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
