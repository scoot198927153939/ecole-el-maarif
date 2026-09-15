<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade_level',
        'academic_year_id',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function assignments()
    {
        return $this->hasMany(ClassSubjectTeacher::class, 'class_id');
    }

    public function schedules()
    {
        return $this->hasManyThrough(Schedule::class, ClassSubjectTeacher::class, 'class_id', 'class_subject_teacher_id');
    }
}