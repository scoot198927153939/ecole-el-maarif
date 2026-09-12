<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function selectClass()
    {
        $classes = ClassRoom::with('academicYear')->orderBy('name')->get();

        return view('attendance.report-select', compact('classes'));
    }

    public function daily(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($validated['date']);
        $dayOfWeek = $date->dayOfWeekIso; // 1=الاثنين ... 7=الأحد (نتجاهل 7)

        $schedules = Schedule::with(['assignment.subject', 'assignment.teacher'])
            ->whereHas('assignment', fn ($q) => $q->where('class_id', $class->id))
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('session_number')
            ->get();

        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('academic_year_id', $class->academic_year_id)
            ->get()
            ->sortBy('student.first_name');

        $attendance = Attendance::whereIn('schedule_id', $schedules->pluck('id'))
            ->where('date', $date->format('Y-m-d'))
            ->get()
            ->groupBy('enrollment_id');

        return view('attendance.daily-report', compact('class', 'date', 'schedules', 'enrollments', 'attendance'));
    }

    public function monthly(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        $year = $validated['year'];
        $month = $validated['month'];

        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $schedules = Schedule::with(['assignment.subject'])
            ->whereHas('assignment', fn ($q) => $q->where('class_id', $class->id))
            ->get();

        $subjects = $schedules->pluck('assignment.subject')->unique('id')->sortBy('name');

        // عدد الحصص المتوقعة لكل مادة هذا الشهر (حسب تكرار يوم الأسبوع بالشهر)
        $expectedPerSubject = [];
        $totalExpected = 0;
        foreach ($schedules as $schedule) {
            $count = 0;
            $cursor = $monthStart->copy();
            while ($cursor->lte($monthEnd)) {
                if ($cursor->dayOfWeekIso === (int) $schedule->day_of_week) {
                    $count++;
                }
                $cursor->addDay();
            }
            $subjectId = $schedule->assignment->subject_id;
            $expectedPerSubject[$subjectId] = ($expectedPerSubject[$subjectId] ?? 0) + $count;
            $totalExpected += $count;
        }

        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('academic_year_id', $class->academic_year_id)
            ->get()
            ->sortBy('student.first_name');

        $allAttendance = Attendance::with('schedule.assignment')
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->get();

        $totalHeld = $allAttendance->unique(fn ($a) => $a->schedule_id.'-'.$a->date)->count();

        $rows = [];
        foreach ($enrollments as $enrollment) {
            $studentAttendance = $allAttendance->where('enrollment_id', $enrollment->id);

            $subjectData = [];
            $totalAbsenceHours = 0;
            $totalPresentHours = 0;

            foreach ($subjects as $subject) {
                $records = $studentAttendance->filter(fn ($a) => $a->schedule->assignment->subject_id === $subject->id);

                $absentCount = $records->whereIn('status', ['absent', 'excused'])->count();
                $presentCount = $records->whereIn('status', ['present', 'late'])->count();

                $absenceHours = $absentCount * 2;
                $presentHours = $presentCount * 2;

                $subjectData[$subject->id] = $absenceHours;
                $totalAbsenceHours += $absenceHours;
                $totalPresentHours += $presentHours;
            }

            $totalRecordedHours = $totalAbsenceHours + $totalPresentHours;
            $attendancePercent = $totalRecordedHours > 0 ? round(($totalPresentHours / $totalRecordedHours) * 100, 1) : null;
            $absencePercent = $totalRecordedHours > 0 ? round(($totalAbsenceHours / $totalRecordedHours) * 100, 1) : null;

            $rows[] = [
                'enrollment' => $enrollment,
                'subjectData' => $subjectData,
                'totalAbsenceHours' => $totalAbsenceHours,
                'attendancePercent' => $attendancePercent,
                'absencePercent' => $absencePercent,
            ];
        }

        $sessionsHeldPercent = $totalExpected > 0 ? round(($totalHeld / $totalExpected) * 100, 1) : null;

        return view('attendance.monthly-report', compact(
            'class', 'year', 'month', 'subjects', 'rows',
            'totalExpected', 'totalHeld', 'sessionsHeldPercent'
        ));
    }
}