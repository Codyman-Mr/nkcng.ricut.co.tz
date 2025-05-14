<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $device_id
 * @property string $latitude
 * @property string $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceLocation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeviceLocation extends Model
{
    protected $fillable = ['device_id', 'latitude', 'longitude'];
    public $timestamps = true;
}