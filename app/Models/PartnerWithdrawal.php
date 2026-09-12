<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'amount',
        'withdrawal_date',
        'note',
        'recorded_by',
    ];

    protected $casts = [
        'withdrawal_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}