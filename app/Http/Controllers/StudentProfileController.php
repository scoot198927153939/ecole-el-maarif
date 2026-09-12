<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Services\GradeCalculator;
use Carbon\Carbon;

class StudentProfileController extends Controller
{
    public function show(Enrollment $enrollment, GradeCalculator $calc)
    {
        $enrollment->load(['student', 'classRoom', 'academicYear']);

        $annualAverage = $calc->annualAverage($enrollment);
        $mention = $calc->annualMention($annualAverage);
        $decision = $calc->decision($annualAverage);

        $term1Comp = $calc->weightedAcrossSubjects($enrollment, fn ($s) => $calc->compositionScore($enrollment, $s, 1));
        $term2Comp = $calc->weightedAcrossSubjects($enrollment, fn ($s) => $calc->compositionScore($enrollment, $s, 2));
        $term3Comp = $calc->weightedAcrossSubjects($enrollment, fn ($s) => $calc->compositionScore($enrollment, $s, 3));

        // ملخص الحضور: آخر 30 يوم
        $since = Carbon::now()->subDays(30)->format('Y-m-d');
        $attendanceRecords = Attendance::where('enrollment_id', $enrollment->id)
            ->where('date', '>=', $since)
            ->get();

        $absentCount = $attendanceRecords->whereIn('status', ['absent', 'excused'])->count();
        $presentCount = $attendanceRecords->whereIn('status', ['present', 'late'])->count();
        $totalRecords = $absentCount + $presentCount;
        $attendancePercent = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : null;

        return view('students.profile', compact(
            'enrollment', 'annualAverage', 'mention', 'decision',
            'term1Comp', 'term2Comp', 'term3Comp',
            'absentCount', 'presentCount', 'attendancePercent'
        ));
    }
}