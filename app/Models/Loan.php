<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }

    public function documents()
    {
        return $this->hasMany(LoanDocument::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function unPaidLoans()
    {
        return self::where('status', 'approved')
            ->where(function ($query) {
                $query->whereRaw('(SELECT SUM(payments.paid_amount) FROM payments WHERE payments.loan_id = loans.id) < loans.loan_required_amount')
                    ->orWhereRaw('(SELECT COUNT(payments.id) FROM payments WHERE payments.loan_id = loans.id) = 0');
            })
            ->with(['user', 'payments'])
            ->get();
    }
}