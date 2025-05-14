<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $total_installation
 * @property string $down_payment
 * @property string $amount_to_finance
 * @property string $cylinder_capacity
 * @property string $min_price
 * @property string $max_price
 * @property string $payment_plan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereAmountToFinance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereCylinderCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereDownPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereMaxPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereMinPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage wherePaymentPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereTotalInstallation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanPackage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LoanPackage extends Model
{
    protected $table = 'loan_packages';
    protected $fillable = [
        'payment_plan'
    ];
}
