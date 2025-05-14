<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string|null $imei
 * @property int $user_id
 * @property string $model
 * @property string $plate_number
 * @property string $vehicle_type
 * @property string $fuel_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GpsDevice|null $gpsDevice
 * @property-read \App\Models\CustomerVehicleGps|null $gpsRecords
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Installation> $installations
 * @property-read int|null $installations_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\CustomerVehicleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereFuelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereImei($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle wherePlateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicle whereVehicleType($value)
 * @mixin \Eloquent
 */
class CustomerVehicle extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, );
    }

    public function installations()
    {
        return $this->hasMany(Installation::class);
    }

    public function gpsRecords()
    {
        return $this->hasOne(CustomerVehicleGps::class, 'imei', 'imei');
    }

    public function gpsDevice()
    {
        return $this->hasOne(GpsDevice::class, 'vehicle_id');
    }

    protected $guarded = [];

    protected $fillable = [
        'imei',
        'user_id',
        'model',
        'plate_number',
        'vehicle_type',
        'fuel_type',
    ];
}



