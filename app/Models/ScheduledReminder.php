<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $loan_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $due_date
 * @property \Illuminate\Support\Carbon $scheduled_at
 * @property string|null $message
 * @property string $status
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Loan $loan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScheduledReminder whereUserId($value)
 * @mixin \Eloquent
 */
class ScheduledReminder extends Model
{
    protected $fillable = [
        'loan_id',
        'user_id',
        'due_date',
        'scheduled_at',
        'message',
        'status',
        'error_message'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'due_date' => 'date',
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
