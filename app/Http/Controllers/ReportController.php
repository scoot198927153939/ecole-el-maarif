<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Services\GradeCalculator;

class ReportController extends Controller
{
    public function show(Enrollment $enrollment, GradeCalculator $calculator)
    {
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

        return view('reports.show', compact('enrollment', 'subjectsData', 'annualAverage', 'decision', 'annualMention'));
    }
}