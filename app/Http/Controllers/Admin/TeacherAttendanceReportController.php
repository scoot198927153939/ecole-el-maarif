<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherAttendanceSession;
use Illuminate\Http\Request;

class TeacherAttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $teachers = Teacher::where('status', 'active')->orderBy('first_name')->get();

        $rows = $teachers->map(function ($teacher) use ($year, $month) {
            $sessions = TeacherAttendanceSession::with('classRoom')
                ->where('teacher_id', $teacher->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderBy('date')
                ->orderBy('session_number')
                ->get();

            $presentCount = $sessions->where('status', 'present')->count();
            $absentCount = $sessions->where('status', 'absent')->count();

            $lateIncidents = $sessions->filter(fn ($s) => $s->late_minutes > 0)
                ->map(fn ($s) => [
                    'date' => $s->date->format('Y-m-d'),
                    'session' => $s->session_number,
                    'minutes' => $s->late_minutes,
                    'class' => $s->classRoom->name ?? '—',
                ]);

            $earlyIncidents = $sessions->filter(fn ($s) => $s->early_leave_minutes > 0)
                ->map(fn ($s) => [
                    'date' => $s->date->format('Y-m-d'),
                    'session' => $s->session_number,
                    'minutes' => $s->early_leave_minutes,
                    'class' => $s->classRoom->name ?? '—',
                ]);

            return [
                'teacher' => $teacher,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'late_count' => $lateIncidents->count(),
                'late_total_minutes' => $lateIncidents->sum('minutes'),
                'late_incidents' => $lateIncidents->values(),
                'early_count' => $earlyIncidents->count(),
                'early_total_minutes' => $earlyIncidents->sum('minutes'),
                'early_incidents' => $earlyIncidents->values(),
            ];
        });

        return view('admin.teacher-attendance-report.index', compact('rows', 'year', 'month'));
    }
}