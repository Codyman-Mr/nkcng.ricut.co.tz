<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property-read \App\Models\CustomerVehicle|null $Vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicleGps newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicleGps newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerVehicleGps query()
 * @mixin \Eloquent
 */
class CustomerVehicleGps extends Model
{

    protected $primaryKey = 'imei';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [ 
        'imei', 
    'lat',
    'lng',
    'speed',
    'timestamp'];

    public function Vehicle(): BelongsTo
    {
        return $this->belongsTo(CustomerVehicle::class);
    }

}
