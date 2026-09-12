<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'term',
        'subject_id',
        'class_id',
        'academic_year_id',
        'coefficient',
        'assessment_date',
        'created_by',
    ];

    protected $casts = [
        'coefficient' => 'decimal:2',
        'assessment_date' => 'date',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}