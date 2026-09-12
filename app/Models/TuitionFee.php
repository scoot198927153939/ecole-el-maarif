<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TuitionFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_level',
        'academic_year_id',
        'amount',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}