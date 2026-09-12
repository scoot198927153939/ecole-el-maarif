<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Grade;
use Mpdf\Mpdf;
use App\Services\GradeCalculator;
use App\Models\Student;

class PdfController extends Controller
{
    private function newMpdf(bool $landscape = false): Mpdf
    {
        $isArabic = app()->getLocale() === 'ar';

        $config = [
            'default_font' => 'dejavusans',
            'directionality' => $isArabic ? 'rtl' : 'ltr',
        ];

        if ($isArabic) {
            $config['mode'] = 'ar';
        }

        if ($landscape) {
            $config['orientation'] = 'L';
        }

        return new Mpdf($config);
    }

    public function singleGrade(Grade $grade)
    {
        $grade->load(['assessment.subject', 'assessment.classRoom', 'enrollment.student', 'enrollment.academicYear']);

        $html = view('pdf.single-grade', compact('grade'))->render();

        $mpdf = $this->newMpdf();

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

public function studentCard(Student $student)
{
    $student->load('guardian');

    $currentClass = Enrollment::with('classRoom.academicYear')
        ->where('student_id', $student->id)
        ->orderByDesc('academic_year_id')
        ->first()?->classRoom;

    $html = view('pdf.student-card', compact('student', 'currentClass'))->render();

    $mpdf = $this->newMpdf();

    $mpdf->WriteHTML($html);

    return response($mpdf->Output('', 'S'))
        ->header('Content-Type', 'application/pdf');
}

    public function classRoster(ClassRoom $class)
    {
        $enrollments = Enrollment::with(['student.guardian'])
            ->where('class_id', $class->id)
            ->where('academic_year_id', $class->academic_year_id)
            ->get()
            ->sortBy(fn ($e) => $e->student->first_name . ' ' . $e->student->last_name)
            ->values();

        $html = view('pdf.class-roster', compact('class', 'enrollments'))->render();

        $mpdf = $this->newMpdf(landscape: true);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    public function assessmentGrades(Assessment $assessment)
    {
        $assessment->load(['subject', 'classRoom', 'academicYear']);

        $enrollments = Enrollment::with('student')
            ->where('class_id', $assessment->class_id)
            ->where('academic_year_id', $assessment->academic_year_id)
            ->get()
            ->sortBy('student.first_name');

        $grades = Grade::where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('enrollment_id');

        $html = view('pdf.assessment-grades', compact('assessment', 'enrollments', 'grades'))->render();

        $mpdf = $this->newMpdf();

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    public function termReport(Enrollment $enrollment, int $term)
    {
        $enrollment->load(['student', 'classRoom', 'academicYear']);

        $grades = Grade::with(['assessment.subject'])
            ->where('enrollment_id', $enrollment->id)
            ->whereHas('assessment', function ($query) use ($term) {
                $query->where('term', $term);
            })
            ->where('is_absent', false)
            ->whereNotNull('score')
            ->get();

        $subjectsData = [];

        foreach ($grades->groupBy('assessment.subject_id') as $subjectId => $subjectGrades) {
            $subject = $subjectGrades->first()->assessment->subject;

            $weightedSum = 0;
            $weightSum = 0;

            foreach ($subjectGrades as $grade) {
                $coefficient = $grade->assessment->coefficient;
                $weightedSum += $grade->score * $coefficient;
                $weightSum += $coefficient;
            }

            $subjectAverage = $weightSum > 0 ? round($weightedSum / $weightSum, 2) : null;

            $subjectsData[] = [
                'subject' => $subject,
                'average' => $subjectAverage,
                'grades' => $subjectGrades,
            ];
        }

        $overallWeightedSum = 0;
        $overallWeightSum = 0;

        foreach ($subjectsData as $data) {
            if ($data['average'] !== null) {
                $overallWeightedSum += $data['average'] * $data['subject']->coefficient;
                $overallWeightSum += $data['subject']->coefficient;
            }
        }

        $termAverage = $overallWeightSum > 0 ? round($overallWeightedSum / $overallWeightSum, 2) : null;
        $mention = $this->getMention($termAverage);

        $html = view('pdf.term-report', compact('enrollment', 'term', 'subjectsData', 'termAverage', 'mention'))->render();

        $mpdf = $this->newMpdf();

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    public function term1Report(Enrollment $enrollment, GradeCalculator $calc)
    {
        $data = $this->buildTerm1Data($enrollment, $calc);

        $html = view('pdf.term1-report', $data)->render();

        $mpdf = $this->newMpdf(landscape: true);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    public function term2Report(Enrollment $enrollment, GradeCalculator $calc)
    {
        $data = $this->buildTerm2Data($enrollment, $calc);

        $html = view('pdf.term2-report', $data)->render();

        $mpdf = $this->newMpdf(landscape: true);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    public function term3Report(Enrollment $enrollment, GradeCalculator $calc)
    {
        $data = $this->buildTerm3Data($enrollment, $calc);

        $html = view('pdf.term3-report', $data)->render();

        $mpdf = $this->newMpdf(landscape: true);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    public function classTerm1Report(ClassRoom $class, GradeCalculator $calc)
    {
        return $this->buildClassTermPdf($class, $calc, 1, 'pdf.term1-report', fn ($e) => $this->buildTerm1Data($e, $calc));
    }

    public function classTerm2Report(ClassRoom $class, GradeCalculator $calc)
    {
        return $this->buildClassTermPdf($class, $calc, 2, 'pdf.term2-report', fn ($e) => $this->buildTerm2Data($e, $calc));
    }

    public function classTerm3Report(ClassRoom $class, GradeCalculator $calc)
    {
        return $this->buildClassTermPdf($class, $calc, 3, 'pdf.term3-report', fn ($e) => $this->buildTerm3Data($e, $calc));
    }

    private function buildClassTermPdf(ClassRoom $class, GradeCalculator $calc, int $term, string $view, callable $dataBuilder)
    {
        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('academic_year_id', $class->academic_year_id)
            ->get()
            ->sortBy('student.first_name');

        $mpdf = $this->newMpdf(landscape: true);

        $first = true;
        foreach ($enrollments as $enrollment) {
            $data = $dataBuilder($enrollment);
            $html = view($view, $data)->render();

            if (! $first) {
                $mpdf->AddPage();
            }
            $mpdf->WriteHTML($html);
            $first = false;
        }

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    private function buildTerm1Data(Enrollment $enrollment, GradeCalculator $calc): array
    {
        $subjects = $calc->applicableSubjects($enrollment);

        $rows = [];
        foreach ($subjects as $subject) {
            $rows[] = [
                'subject' => $subject,
                'tests' => $calc->testScoresForTerm($enrollment, $subject, 1),
                'testsAvg' => $calc->cumulativeTestsAverage($enrollment, $subject, 1),
                'comp1' => $calc->compositionScore($enrollment, $subject, 1),
            ];
        }

        $overallTestsAvg = $calc->weightedAcrossSubjects($enrollment, fn ($s) => $calc->cumulativeTestsAverage($enrollment, $s, 1));
        $overallComp1Avg = $calc->weightedAcrossSubjects($enrollment, fn ($s) => $calc->compositionScore($enrollment, $s, 1));
        $overallAverage = $calc->simpleAverageOf([$overallTestsAvg, $overallComp1Avg]);

        return [
            'enrollment' => $enrollment,
            'rows' => $rows,
            'overallTestsAvg' => $overallTestsAvg,
            'overallComp1Avg' => $overallComp1Avg,
            'overallAverage' => $overallAverage,
            'mention' => $calc->annualMention($overallAverage),
            'decision' => $calc->decision($overallAverage),
        ];
    }

    private function buildTerm2Data(Enrollment $enrollment, GradeCalculator $calc): array
    {
        $subjects = $calc->applicableSubjects($enrollment);

        $rows = [];
        foreach ($subjects as $subject) {
            $rows[] = [
                'subject' => $subject,
                'testsT1' => $calc->testScoresForTerm($enrollment, $subject, 1),
                'comp1' => $calc->compositionScore($enrollment, $subject, 1),
                'testsT2' => $calc->testScoresForTerm($enrollment, $subject, 2),
                'comp2' => $calc->compositionScore($enrollment, $subject, 2),
                'testsAvg' => $calc->cumulativeTestsAverage($enrollment, $subject, 2),
            ];
        }

        $overallTestsAvg = $calc->weightedAcrossSubjects($enrollment, fn ($s) => $calc->cumulativeTestsAverage($enrollment, $s, 2));
        $overallComp1Avg = $calc->weightedAcrossSubjects($enrollment, fn ($s) => $calc->compositionScore($enrollment, $s, 1));
        $overallComp2Avg = $calc->weightedAcrossSubjects($enrollment, fn ($s) => $calc->compositionScore($enrollment, $s, 2));
        $overallAverage = $calc->simpleAverageOf([$overallTestsAvg, $overallComp1Avg, $overallComp2Avg]);

        return [
            'enrollment' => $enrollment,
            'rows' => $rows,
            'overallTestsAvg' => $overallTestsAvg,
            'overallComp1Avg' => $overallComp1Avg,
            'overallComp2Avg' => $overallComp2Avg,
            'overallAverage' => $overallAverage,
            'mention' => $calc->annualMention($overallAverage),
            'decision' => $calc->decision($overallAverage),
        ];
    }

    private function buildTerm3Data(Enrollment $enrollment, GradeCalculator $calc): array
    {
        $subjects = $calc->applicableSubjects($enrollment);

        $rows = [];
        foreach ($subjects as $subject) {
            $rows[] = [
                'subject' => $subject,
                'testsAvg' => $calc->cumulativeTestsAverage($enrollment, $subject, 3),
                'comp1' => $calc->compositionScore($enrollment, $subject, 1),
                'comp2' => $calc->compositionScore($enrollment, $subject, 2),
                'comp3' => $calc->compositionScore($enrollment, $subject, 3),
                'yearAvg' => $calc->subjectYearAverage($enrollment, $subject),
            ];
        }

        $overallAverage = $calc->annualAverage($enrollment);

        return [
            'enrollment' => $enrollment,
            'rows' => $rows,
            'overallAverage' => $overallAverage,
            'mention' => $calc->annualMention($overallAverage),
            'decision' => $calc->decision($overallAverage),
        ];
    }

    private function getMention(?float $average): string
    {
        if ($average === null) {
            return '-';
        }

        if ($average >= 17) return __('messages.pdf_mention_felicitations');
        if ($average >= 15) return __('messages.pdf_mention_honor');
        if ($average >= 12) return __('messages.pdf_mention_good');
        if ($average >= 10) return __('messages.pdf_mention_acceptable');
        if ($average >= 9) return __('messages.pdf_mention_needs_more_work');
        if ($average >= 8) return __('messages.pdf_mention_warning');

        return __('messages.pdf_mention_failed');
    }
}