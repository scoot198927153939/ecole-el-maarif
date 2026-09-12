<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'staff_attendance';

    protected $fillable = [
        'staff_type',
        'staff_id',
        'date',
        'hours',
        'status',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
    ];

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // يرجع الموديل الفعلي (Teacher أو StaffMember) لهذا السجل
    public function staffMember()
    {
        if ($this->staff_type === 'teacher') {
            return Teacher::find($this->staff_id);
        }

        return StaffMember::find($this->staff_id);
    }
}