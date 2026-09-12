<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'enrollment_status',
        'enrollment_date',
        'discount_percentage',
        'next_payment_due_date',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'discount_percentage' => 'decimal:2',
        'next_payment_due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function withdrawalRefunds()
{
    return $this->hasMany(WithdrawalRefund::class);
}

    public function tuitionAmount(): float
    {
        $level = preg_replace('/\d+$/', '', $this->classRoom->name);

        $fee = TuitionFee::where('grade_level', $level)
            ->where('academic_year_id', $this->academic_year_id)
            ->first();

        return $fee ? (float) $fee->amount : 0;
    }

    public function requiredAmount(): float
    {
        $amount = $this->tuitionAmount();
        $discount = $amount * ((float) $this->discount_percentage / 100);

        return round($amount - $discount, 2);
    }

    public function paidAmount(): float
    {
        return (float) $this->feePayments()->sum('amount');
    }

    public function remainingAmount(): float
    {
        return round($this->requiredAmount() - $this->paidAmount(), 2);
    }
}