<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    use HasFactory;

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    protected $fillable = [
        'loan_id',
        'users_id',
        'payment_date',
        'paid_amount',
        'payment_method',
        'payment_description',
    ];
}
