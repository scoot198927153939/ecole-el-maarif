<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function index()
    {
        $schedules = Schedule::with(['assignment.classRoom', 'assignment.subject', 'assignment.teacher'])
            ->orderBy('day_of_week')
            ->orderBy('session_number')
            ->get();

        $days = $this->days();

        return view('admin.schedules.index', compact('schedules', 'days'));
    }

    public function create()
    {
        $assignments = ClassSubjectTeacher::with(['classRoom', 'subject', 'teacher'])->get();
        $days = $this->days();

        return view('admin.schedules.create', compact('assignments', 'days'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:1,6',
            'session_number' => 'required|integer|between:1,3',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'class_subject_teacher_id' => 'required|exists:class_subject_teacher,id',
        ]);

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', __('messages.flash_schedule_created'));
    }

    public function edit(Schedule $schedule)
    {
        $assignments = ClassSubjectTeacher::with(['classRoom', 'subject', 'teacher'])->get();
        $days = $this->days();

        return view('admin.schedules.edit', compact('schedule', 'assignments', 'days'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:1,6',
            'session_number' => 'required|integer|between:1,3',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'class_subject_teacher_id' => 'required|exists:class_subject_teacher,id',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', __('messages.flash_schedule_updated'));
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', __('messages.flash_schedule_deleted'));
    }
}