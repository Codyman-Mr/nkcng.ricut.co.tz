<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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
