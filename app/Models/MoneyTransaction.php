<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'money_source_id',
        'direction',
        'amount',
        'description',
        'transaction_date',
        'document_path',
        'recorded_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function moneySource()
    {
        return $this->belongsTo(MoneySource::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}