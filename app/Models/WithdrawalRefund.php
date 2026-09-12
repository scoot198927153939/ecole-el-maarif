<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'refund_amount',
        'refund_date',
        'method',
        'transfer_service',
        'transfer_number',
        'sender_account',
        'receiver_account',
        'transfer_photo_path',
        'money_source_id',
        'note',
        'recorded_by',
    ];

    protected $casts = [
        'refund_date' => 'date',
        'refund_amount' => 'decimal:2',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function moneySource()
    {
        return $this->belongsTo(MoneySource::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}