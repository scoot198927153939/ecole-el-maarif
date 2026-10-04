<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_type',
        'staff_id',
        'year',
        'month',
        'amount',
        'money_transaction_id',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function moneyTransaction()
    {
        return $this->belongsTo(MoneyTransaction::class);
    }
}
