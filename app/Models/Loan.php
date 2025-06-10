<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonInterval;


class Loan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'time_to_next_payment',
        'due_date',
    ];

    protected $fillable = [
        'user_id',
        'installation_id',
        'loan_type',
        'loan_required_amount',
        'loan_payment_plan',
        'loan_start_date',
        'loan_end_date',
        'status',
        'rejection_reason'
    ];

    public function getTimeToNextPaymentAttribute()
    {
        // Only calculate for approved loans
        if ($this->status !== 'approved') {
            return null;
        }

        // Check if loan is fully paid
        $totalPaid = $this->payments->sum('paid_amount');
        if ($totalPaid >= $this->loan_required_amount) {
            return null;
        }

        // Check if loan period has ended
        $today = Carbon::now();
        $endDate = Carbon::parse($this->loan_end_date);
        if ($today->gt($endDate)) {
            return null;
        }

        // Calculate next payment date
        $nextPaymentDate = $this->calculateNextPaymentDate();

        return $nextPaymentDate
            ? $today->diffInDays($nextPaymentDate, false) // Returns negative if overdue
            : null;
    }

    public function getDueDateAttribute()
    {
        return $this->calculateNextPaymentDate();
    }


    protected function calculateNextPaymentDate()
    {
        $startDate = Carbon::parse($this->loan_start_date);
        $endDate = Carbon::parse($this->loan_end_date);
        $today = Carbon::now();

        // Edge case: Loan hasn't started yet
        if ($today->lt($startDate)) {
            return $startDate->lte($endDate) ? $startDate : null;
        }

        // Calculate intervals based on payment plan
        $intervalFn = match ($this->loan_payment_plan) {
            'weekly' => fn() => $startDate->diffInWeeks($today),
            'bi-weekly' => fn() => floor($startDate->diffInDays($today) / 14),
            'monthly' => fn() => $startDate->diffInMonths($today),
        };

        $intervalsPassed = $intervalFn();
        $nextPayment = $startDate->copy()->add(
            $this->getPaymentInterval(),
            $intervalsPassed + 1 // Add 1 interval to get the NEXT payment
        );

        // Ensure next payment is within the loan period
        return $nextPayment->lte($endDate) ? $nextPayment : null;
    }

    protected function getPaymentInterval()
    {
        return match ($this->loan_payment_plan) {
            'weekly' => CarbonInterval::week(),
            'bi-weekly' => CarbonInterval::weeks(2),
            'monthly' => CarbonInterval::month(),
            default => throw new \Exception("Invalid payment plan"),
        };
    }

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

    public function governmentGuarantor()
    {
        return $this->hasOne(GovernmentGuarantor::class);
    }

    public function privateGuarantor()
    {
        return $this->hasOne(PrivateGuarantor::class);
    }

    public function reminderLogs()
    {
        return $this->hasMany(RepaymentReminderLog::class);
    }

    public function loanPackage()
    {
        return $this->belongsTo(LoanPackage::class);
    }

}
