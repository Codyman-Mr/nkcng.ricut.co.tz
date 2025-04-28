<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPackage extends Model
{
    protected $table = 'loan_packages';
    protected $fillable = [
        'payment_plan'
    ];
}
