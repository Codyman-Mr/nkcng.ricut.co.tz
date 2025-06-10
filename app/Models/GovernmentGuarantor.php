<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GovernmentGuarantor extends Model
{
   use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'first_name',
        'last_name',
        'phone_number',
        'nida_no',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}

