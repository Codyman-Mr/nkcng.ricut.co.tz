<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'user_id',
        'paid_amount',
        'transaction_id',
        'external_id',
        'status',
        'provider',
        'callback_data',
        'payment_method',
        'payment_date',
    ];

    protected $casts = [
        'callback_data' => 'array',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
