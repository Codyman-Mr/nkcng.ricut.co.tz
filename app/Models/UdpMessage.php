<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UdpMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UdpMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UdpMessage query()
 * @mixin \Eloquent
 */
class UdpMessage extends Model
{
    protected $fillable = ['ip', 'port', 'latitude', 'longitude'];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}
