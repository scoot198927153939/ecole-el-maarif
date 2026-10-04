<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyTransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'money_transaction_id',
        'user_id',
        'action',
        'description',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(MoneyTransaction::class, 'money_transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
