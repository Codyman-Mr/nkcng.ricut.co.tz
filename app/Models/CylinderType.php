<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CylinderType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'capacity',
        'loan_package_id',
    ];

    public function loanPackage()
    {
        return $this->belongsTo(LoanPackage::class, 'loan_package_id');
    }
}
