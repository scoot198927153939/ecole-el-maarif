<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\LessonLog;
use App\Models\Schedule;
use App\Models\Student;
use App\Services\GradeCalculator;

class DashboardController extends Controller
{
    public function index()
    {
        $guardian = auth()->user()->guardian;

        $students = $guardian
            ? $guardian->students()
                ->with(['enrollments.classRoom.academicYear'])
                ->orderBy('first_name')
                ->get()
                ->map(function ($student) {
                    $student->current_enrollment = $student->enrollments->sortByDesc('academic_year_id')->first();

                    return $student;
                })
            : collect();

        return view('guardian.dashboard', compact('students'));
    }

    public function showStudent(Student $student, GradeCalculator $calculator)
    {
        $guardian = auth()->user()->guardian;

        abort_unless($guardian && $student->guardian_id === $guardian->id, 403, __('messages.guardian_portal_access_denied'));

        $enrollment = $student->enrollments()
            ->with('classRoom.academicYear')
            ->orderByDesc('academic_year_id')
            ->first();

        if (! $enrollment) {
            return view('guardian.student', [
                'student' => $student,
                'enrollment' => null,
            ]);
        }

        $subjects = $calculator->applicableSubjects($enrollment);
        $subjectsData = [];
        foreach ($subjects as $subject) {
            $subjectsData[] = [
                'subject' => $subject,
                'average' => $calculator->subjectYearAverage($enrollment, $subject),
            ];
        }

        $annualAverage = $calculator->annualAverage($enrollment);
        $decision = $calculator->decision($annualAverage);
        $annualMention = $calculator->annualMention($annualAverage);

        $requiredAmount = $enrollment->requiredAmount();
        $paidAmount = $enrollment->paidAmount();
        $remainingAmount = $enrollment->remainingAmount();
        $payments = $enrollment->feePayments()->orderBy('payment_date', 'desc')->get();

        $lessonLogs = LessonLog::with(['assignment.subject', 'assignment.teacher'])
            ->whereHas('assignment', function ($q) use ($enrollment) {
                $q->where('class_id', $enrollment->class_id);
            })
            ->orderBy('lesson_date', 'desc')
            ->limit(20)
            ->get();

        $schedule = Schedule::with(['assignment.subject', 'assignment.teacher'])
            ->whereHas('assignment', function ($q) use ($enrollment) {
                $q->where('class_id', $enrollment->class_id);
            })
            ->orderBy('day_of_week')
            ->orderBy('session_number')
            ->get();

        return view('guardian.student', compact(
            'student', 'enrollment', 'subjectsData', 'annualAverage', 'decision', 'annualMention',
            'requiredAmount', 'paidAmount', 'remainingAmount', 'payments', 'lessonLogs', 'schedule'
        ));
    }
}
