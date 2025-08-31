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
         'nida_number',
        'loan_required_amount',
        'loan_payment_plan',
        'loan_start_date',
        'loan_package',
        'cylinder_capacity',
        'loan_end_date',
        'status',
        'applicant_name', 
        'applicant_phone_number',
        'rejection_reason'
    ];


    
public function getTimeToNextPaymentAttribute() 
{
    if (!$this->loan_start_date || !$this->loan_payment_plan || $this->status !== 'approved') {
        return null;
    }

    $timezone = 'Africa/Dar_es_Salaam';
    $startDate = Carbon::parse($this->loan_start_date, $timezone)->startOfDay();
    $today = Carbon::now($timezone)->startOfDay();
    $nextPaymentDate = $startDate->copy();

    switch (strtolower($this->loan_payment_plan)) {
        case 'weekly':
            while ($nextPaymentDate->lessThan($today)) {   // < instead of <=
                $nextPaymentDate->addDays(7);
            }
            break;

        case 'bi-weekly':
            while ($nextPaymentDate->lessThan($today)) {
                $nextPaymentDate->addDays(14);
            }
            break;

        case 'monthly':
            while ($nextPaymentDate->lessThan($today)) {
                $nextPaymentDate->addMonth();
            }
            break;

        default:
            return null;
    }

    if ($this->loan_end_date) {
        $loanEnd = Carbon::parse($this->loan_end_date, $timezone)->startOfDay();
        if ($nextPaymentDate->greaterThan($loanEnd)) {
            return null;
        }
    }

    $daysToNext = $today->diffInDays($nextPaymentDate, false);

    return $daysToNext >= 0 ? $daysToNext : null;
}

public function getDaysPastDueAttribute()
{
    if (!$this->loan_start_date || !$this->loan_payment_plan || $this->status !== 'approved') {
        return null;
    }

    $startDate = Carbon::parse($this->loan_start_date)->startOfDay();
    $today = Carbon::now('Africa/Dar_es_Salaam')->startOfDay();
    $lastPaymentDate = $startDate->copy();

    switch (strtolower($this->loan_payment_plan)) {
        case 'weekly':
            while ($lastPaymentDate->addDays(7)->lessThanOrEqualTo($today)) {}
            $lastPaymentDate->subDays(7);
            break;

        case 'bi-weekly':
            while ($lastPaymentDate->addDays(14)->lessThanOrEqualTo($today)) {}
            $lastPaymentDate->subDays(14);
            break;

        case 'monthly':
            while ($lastPaymentDate->addMonth()->lessThanOrEqualTo($today)) {}
            $lastPaymentDate->subMonth();
            break;

        default:
            return null;
    }

    if ($this->loan_end_date) {
        $loanEnd = Carbon::parse($this->loan_end_date)->startOfDay();
        if ($lastPaymentDate->greaterThan($loanEnd)) {
            return null;
        }
    }

    // Hesabu siku zilizopita tangu tarehe ya malipo
    $daysLate = $lastPaymentDate->diffInDays($today, false);
    return $daysLate > 0 ? $daysLate : null;
}


public function calculateDaysPastDue()
{
    if ($this->status !== 'approved' || !$this->loan_end_date) {
        return null;
    }

    $totalPaid = $this->payments->sum('paid_amount');

    if ($totalPaid >= $this->loan_required_amount) {
        return 0; 
    }

    $today = Carbon::now()->startOfDay();
    $loanEndDate = Carbon::parse($this->loan_end_date)->startOfDay();

    if ($today->gt($loanEndDate)) {
        return $today->diffInDays($loanEndDate); 
    }

    return 0; 
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
            $intervalsPassed + 1 
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

public function getDaysOverdueAttribute()
{
    $today = Carbon::today();

    
    $dueDate = $this->due_date instanceof Carbon ? $this->due_date : Carbon::parse($this->due_date);

    if ($today->gt($dueDate)) {
        
        
        return $dueDate->diffInDays($today);
    }

    return 0;
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
