<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassSubjectTeacher;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
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

    // الأوقات الثابتة الثلاثة لكل يوم دراسي، حسب رقم الحصة
    private function sessionTimes(): array
    {
        return [
            1 => ['08:00', '10:00'],
            2 => ['10:00', '12:00'],
            3 => ['12:00', '14:00'],
        ];
    }

    // صفحة اختيار القسم لتعديل جدوله
    public function index()
    {
        $classes = ClassRoom::with('academicYear')
            ->withCount('schedules')
            ->orderBy('name')
            ->get();

        return view('admin.schedules.index', compact('classes'));
    }

    // شبكة الجدول الأسبوعي لقسم واحد (٣ حصص × ٦ أيام)
    public function show(ClassRoom $class)
    {
        $days = $this->days();
        $sessionTimes = $this->sessionTimes();

        $assignments = ClassSubjectTeacher::with(['subject', 'teacher'])
            ->where('class_id', $class->id)
            ->get();

        $schedules = Schedule::with(['assignment.subject', 'assignment.teacher'])
            ->whereHas('assignment', fn ($q) => $q->where('class_id', $class->id))
            ->get()
            ->keyBy(fn ($s) => $s->day_of_week.'-'.$s->session_number);

        return view('admin.schedules.show', compact('class', 'days', 'sessionTimes', 'assignments', 'schedules'));
    }

    // تعيين أو تفريغ خلية واحدة في شبكة القسم (يوم + رقم حصة)
    public function storeSlot(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:1,6',
            'session_number' => 'required|integer|between:1,3',
            'class_subject_teacher_id' => 'nullable|exists:class_subject_teacher,id',
        ]);

        $existing = Schedule::whereHas('assignment', fn ($q) => $q->where('class_id', $class->id))
            ->where('day_of_week', $validated['day_of_week'])
            ->where('session_number', $validated['session_number'])
            ->first();

        if (empty($validated['class_subject_teacher_id'])) {
            $existing?->delete();

            return back()->with('success', __('messages.flash_schedule_deleted'));
        }

        $belongsToClass = ClassSubjectTeacher::where('id', $validated['class_subject_teacher_id'])
            ->where('class_id', $class->id)
            ->exists();

        abort_unless($belongsToClass, 403);

        [$start, $end] = $this->sessionTimes()[$validated['session_number']];

        if ($existing) {
            $existing->update([
                'class_subject_teacher_id' => $validated['class_subject_teacher_id'],
                'start_time' => $start,
                'end_time' => $end,
            ]);
        } else {
            Schedule::create([
                'day_of_week' => $validated['day_of_week'],
                'session_number' => $validated['session_number'],
                'start_time' => $start,
                'end_time' => $end,
                'class_subject_teacher_id' => $validated['class_subject_teacher_id'],
            ]);
        }

        return back()->with('success', __('messages.flash_schedule_updated'));
    }

    // الجدول العام: كل الأقسام مجتمعة في صفحة واحدة
    public function master()
    {
        $days = $this->days();
        $sessionTimes = $this->sessionTimes();

        $classes = ClassRoom::with('academicYear')->orderBy('name')->get();

        $schedules = Schedule::with(['assignment.classRoom', 'assignment.subject', 'assignment.teacher'])
            ->get()
            ->keyBy(fn ($s) => $s->assignment->class_id.'-'.$s->day_of_week.'-'.$s->session_number);

        return view('admin.schedules.master', compact('classes', 'days', 'sessionTimes', 'schedules'));
    }
}
