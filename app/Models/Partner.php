<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'percentage',
        'academic_year_id',
        'status',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(PartnerWithdrawal::class);
    }

    public function getTotalWithdrawnAttribute(): float
    {
        return (float) $this->withdrawals->sum('amount');
    }
}