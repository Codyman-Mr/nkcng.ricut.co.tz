<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
