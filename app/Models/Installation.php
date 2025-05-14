<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $customer_vehicle_id
 * @property int $cylinder_type_id
 * @property string $status
 * @property string $payment_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CustomerVehicle $customerVehicle
 * @property-read \App\Models\CylinderType $cylinderType
 * @property-read \App\Models\Loan|null $loan
 * @method static \Database\Factories\InstallationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereCustomerVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereCylinderTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Installation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Installation extends Model
{
    use HasFactory;

    public function customerVehicle()
    {
        return $this->belongsTo(CustomerVehicle::class, "customer_vehicle_id");
    }

    public function cylinderType()
    {
        return $this->belongsTo(CylinderType::class, "cylinder_type_id");
    }

    public function loan()
    {
        return $this->hasOne(Loan::class);
    }

    protected $fillable = [
        'customer_vehicle_id',
        'cylinder_type_id',
        'status',
        'payment_type',
    ];
}
