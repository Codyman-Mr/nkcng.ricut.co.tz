<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
 
    protected $table = 'sms_logs';

 
    public $timestamps = false;

    protected $fillable = [
        'loan_id',
        'applicant_name',
        'phone_number',
        'message',
        'sent_status',
        'reason',
        'sent_at',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
