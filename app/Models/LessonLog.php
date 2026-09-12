<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_subject_teacher_id',
        'title',
        'topic',
        'lesson_date',
        'pdf_path',
    ];

    protected $casts = [
        'lesson_date' => 'date',
    ];

    public function assignment()
    {
        return $this->belongsTo(ClassSubjectTeacher::class, 'class_subject_teacher_id');
    }

    public function photos()
    {
        return $this->hasMany(LessonPhoto::class)->orderBy('id');
    }
}