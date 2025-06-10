<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPackage extends Model
{

    protected $table = 'loan_packages';

    protected $fillable = [
        'name',
        'description',
        'total_installation',
        'down_payment',
        'amount_to_finance',
        'cylinder_capacity',
        'min_price',
        'max_price',
        'payment_plan',
    ];

    public function cylinder()
    {
        return $this->hasOne(CylinderType::class, 'loan_package_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }



}
