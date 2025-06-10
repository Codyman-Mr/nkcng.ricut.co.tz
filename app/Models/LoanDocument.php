<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $loan_id
 * @property string $document_type
 * @property string $document_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Loan $loan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDocument whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LoanDocument extends Model
{
    protected $table = 'loan_documents';

    protected $fillable = [
        'loan_id',
        'document_type',
        'document_path',
        'created_at',
        'updated_at',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
