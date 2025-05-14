<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonInterval;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $installation_id
 * @property string $loan_type
 * @property string $loan_required_amount
 * @property string $loan_payment_plan
 * @property string|null $loan_start_date
 * @property string|null $loan_end_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $rejection_reason
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanDocument> $documents
 * @property-read int|null $documents_count
 * @property-read mixed $time_to_next_payment
 * @property-read \App\Models\Installation $installation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\LoanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereInstallationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanPaymentPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanRequiredAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereLoanType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereUserId($value)
 * @mixin \Eloquent
 */
class Loan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'time_to_next_payment',
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
}
