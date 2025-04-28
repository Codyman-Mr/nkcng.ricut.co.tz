<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
