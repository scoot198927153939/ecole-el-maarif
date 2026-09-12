<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'last_name',
        'phone',
        'email',
        'specialization',
        'hire_date',
        'status',
        'salary_type',
        'fixed_salary',
        'hourly_rate',
        'is_partner',
        'payment_basis',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_partner' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return StaffAttendance::where('staff_type', 'teacher')->where('staff_id', $this->id);
    }
    public function advances()
{
    return StaffAdvance::where('staff_type', 'teacher')->where('staff_id', $this->id);
}

public function scheduledSessionsPerWeek(): int
{
    return \App\Models\Schedule::whereHas('assignment', function ($q) {
        $q->where('teacher_id', $this->id);
    })->count();
}
public function hoursInMonth(int $year, int $month): float
{
    return (float) \App\Models\TeacherAttendanceSession::where('teacher_id', $this->id)
        ->where('status', 'present')
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->count() * 2;
}
    public function calculatedSalary(int $year, int $month): float
    {
        if ($this->salary_type === 'fixed') {
            return (float) $this->fixed_salary;
        }

        return $this->hoursInMonth($year, $month) * (float) $this->hourly_rate;
    }
}