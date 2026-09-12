<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Support\TeacherAccess;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private function days(): array
    {
        return [
            1 => __('messages.day_monday'),
            2 => __('messages.day_tuesday'),
            3 => __('messages.day_wednesday'),
            4 => __('messages.day_thursday'),
            5 => __('messages.day_friday'),
            6 => __('messages.day_saturday'),
        ];
    }

    // صفحة اختيار اليوم والحصة
    public function select()
    {
        $days = $this->days();

        return view('attendance.select', compact('days'));
    }

    // قائمة الحصص المجدولة ليوم معين
    public function schedulesForDay(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:1,6',
        ]);

        $schedulesQuery = Schedule::with(['assignment.classRoom', 'assignment.subject', 'assignment.teacher'])
            ->where('day_of_week', $validated['day_of_week']);

        if (TeacherAccess::isRestricted()) {
            $teacherId = TeacherAccess::teacherId();
            $schedulesQuery->whereHas('assignment', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });
        }

        $schedules = $schedulesQuery->orderBy('session_number')->get();

        $days = $this->days();
        $selectedDay = $validated['day_of_week'];

        return view('attendance.schedules', compact('schedules', 'days', 'selectedDay'));
    }

    // عرض قائمة الطلاب لتسجيل حضور حصة معينة بتاريخ معين
    public function index(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $schedule->load(['assignment.classRoom', 'assignment.subject', 'assignment.teacher']);

        if (TeacherAccess::isRestricted() && optional($schedule->assignment)->teacher_id !== TeacherAccess::teacherId()) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $enrollments = Enrollment::with('student')
            ->where('class_id', $schedule->assignment->class_id)
            ->where('academic_year_id', $schedule->assignment->classRoom->academic_year_id)
            ->get()
            ->sortBy('student.first_name');

        $existingAttendance = Attendance::where('schedule_id', $schedule->id)
            ->where('date', $validated['date'])
            ->get()
            ->keyBy('enrollment_id');

        $date = $validated['date'];

        return view('attendance.index', compact('schedule', 'enrollments', 'existingAttendance', 'date'));
    }

    // حفظ حضور كل طلاب الحصة دفعة وحدة
    public function store(Request $request, Schedule $schedule)
    {
        $schedule->loadMissing('assignment');

        if (TeacherAccess::isRestricted() && optional($schedule->assignment)->teacher_id !== TeacherAccess::teacherId()) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'required|in:present,absent,late,excused',
        ]);

        foreach ($validated['status'] as $enrollmentId => $status) {
            Attendance::updateOrCreate(
                [
                    'enrollment_id' => $enrollmentId,
                    'schedule_id' => $schedule->id,
                    'date' => $validated['date'],
                ],
                [
                    'status' => $status,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('attendance.select')
            ->with('success', __('messages.flash_attendance_saved', ['date' => $validated['date']]));
    }
}