<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CylinderTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CylinderType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CylinderType extends Model
{
    use HasFactory;
}
