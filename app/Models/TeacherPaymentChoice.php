<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherPaymentChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'year',
        'month',
        'choice',
        'recorded_by',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}