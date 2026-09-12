<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'phone',
        'salary_type',
        'fixed_salary',
        'hourly_rate',
        'status',
    ];

    public function attendances()
    {
        return StaffAttendance::where('staff_type', 'staff_member')->where('staff_id', $this->id);
    }

    public function hoursInMonth(int $year, int $month): float
    {
        return (float) $this->attendances()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', 'present')
            ->sum('hours');
    }

    public function calculatedSalary(int $year, int $month): float
    {
        if ($this->salary_type === 'fixed') {
            return (float) $this->fixed_salary;
        }

        return $this->hoursInMonth($year, $month) * (float) $this->hourly_rate;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'director' => __('messages.staff_members_role_director'),
            'supervisor' => __('messages.staff_members_role_supervisor'),
            'accountant' => __('messages.staff_members_role_accountant'),
            'cleaner' => __('messages.staff_members_role_cleaner'),
            'guard' => __('messages.staff_members_role_guard'),
            default => $this->role,
        };
    }
}