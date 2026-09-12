<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_id',
        'student_number',
        'national_id',
        'school_number',
        'first_name',
        'last_name',
        'birth_date',
        'birth_place',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}