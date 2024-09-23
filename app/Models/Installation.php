<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    use HasFactory;

    public function customerVehicle()
    {
        return $this->belongsTo(CustomerVehicle::class);
    }

    public function cylinderType()
    {
        return $this->belongsTo(CylinderType::class);
    }

    public function loan()
    {
        return $this->hasOne(Loan::class);
    }

    protected $guarded = [];
}
