<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_log_id',
        'photo_path',
    ];

    public function lessonLog()
    {
        return $this->belongsTo(LessonLog::class);
    }
}