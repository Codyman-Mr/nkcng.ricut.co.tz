<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepaymentReminderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'type',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
