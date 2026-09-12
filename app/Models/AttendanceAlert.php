<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'date',
        'message',
        'is_read',
    ];

    protected $casts = [
        'date' => 'date',
        'is_read' => 'boolean',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}