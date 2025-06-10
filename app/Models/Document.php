<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['loan_id', 'file_name', 'file_path', 'file_type', 'mime_type', 'file_size'];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
