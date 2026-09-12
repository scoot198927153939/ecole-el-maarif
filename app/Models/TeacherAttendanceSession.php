<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TeacherAttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'date',
        'session_number',
        'status',
        'actual_start',
        'actual_end',
        'class_id',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // الأوقات المعيارية الثابتة لكل حصة
    public const STANDARD_TIMES = [
        1 => ['start' => '08:00', 'end' => '09:50'],
        2 => ['start' => '10:10', 'end' => '11:55'],
        3 => ['start' => '12:10', 'end' => '13:50'],
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ساعات الحضور المحسوبة للدفع: ساعتان ثابتتان إن حضر، صفر إن غاب
    public function getPayableHoursAttribute(): float
    {
        return $this->status === 'present' ? 2.0 : 0.0;
    }

    // دقائق التأخر (للملاحظة فقط، لا تدخل في الحساب)
    public function getLateMinutesAttribute(): int
    {
        if ($this->status !== 'present' || ! $this->actual_start) {
            return 0;
        }

        $standard = self::STANDARD_TIMES[$this->session_number]['start'];
        $expected = Carbon::parse($standard);
        $actual = Carbon::parse($this->actual_start);

        return $actual->greaterThan($expected) ? $expected->diffInMinutes($actual) : 0;
    }

    // دقائق الخروج المبكر (للملاحظة فقط، لا تدخل في الحساب)
    public function getEarlyLeaveMinutesAttribute(): int
    {
        if ($this->status !== 'present' || ! $this->actual_end) {
            return 0;
        }

        $standard = self::STANDARD_TIMES[$this->session_number]['end'];
        $expected = Carbon::parse($standard);
        $actual = Carbon::parse($this->actual_end);

        return $actual->lessThan($expected) ? $actual->diffInMinutes($expected) : 0;
    }
}