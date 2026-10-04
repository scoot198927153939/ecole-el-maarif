<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_type',
        'staff_id',
        'amount',
        'date_given',
        'note',
        'recorded_by',
        'money_transaction_id',
    ];

    protected $casts = [
        'date_given' => 'date',
        'amount' => 'decimal:2',
    ];

    public function deductions()
    {
        return $this->hasMany(StaffAdvanceDeduction::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function moneyTransaction()
    {
        return $this->belongsTo(MoneyTransaction::class);
    }

    public function staffMember()
    {
        if ($this->staff_type === 'teacher') {
            return Teacher::find($this->staff_id);
        }

        return StaffMember::find($this->staff_id);
    }

    public function getTotalDeductedAttribute(): float
    {
        return (float) $this->deductions->sum('amount');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return (float) $this->amount - $this->total_deducted;
    }
}