<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAdvanceDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_advance_id',
        'amount',
        'year',
        'month',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function advance()
    {
        return $this->belongsTo(StaffAdvance::class, 'staff_advance_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}