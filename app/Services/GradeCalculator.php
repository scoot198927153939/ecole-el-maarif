<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Subject;

class GradeCalculator
{
    // يستخرج رمز المستوى من اسم الشعبة (مثال: "1AS1" => "1AS")
    public function levelCode(Enrollment $enrollment): string
    {
        return preg_replace('/\d+$/', '', $enrollment->classRoom->name);
    }

    // كل المواد المطبّقة على هذا الطالب (حسب مستواه، وباستثناء أي مادة ضاربها صفر)
    public function applicableSubjects(Enrollment $enrollment)
    {
        return Subject::where('grade_level', $this->levelCode($enrollment))
            ->where('coefficient', '>', 0)
            ->get();
    }

    // معدل مادة واحدة للسنة كاملة (الصيغة الرسمية: 3×معدل_الاختبارات + تأليف1×1 + تأليف2×2 + تأليف3×3 / القاسم)
    public function subjectYearAverage(Enrollment $enrollment, Subject $subject): ?float
    {
        $grades = Grade::with('assessment')
            ->where('enrollment_id', $enrollment->id)
            ->where('subject_id', $subject->id)
            ->where('is_absent', false)
            ->whereNotNull('score')
            ->get();

        $testScores = $grades->filter(fn ($g) => $g->assessment->type === 'test')->pluck('score');
        $devoirAvg = $testScores->count() > 0 ? $testScores->avg() : null;

        $comp = [1 => null, 2 => null, 3 => null];
        foreach ($grades->filter(fn ($g) => $g->assessment->type === 'exam') as $g) {
            $comp[$g->assessment->term] = (float) $g->score;
        }

        $weightedSum = 0;
        $divisor = 9;

        if ($devoirAvg !== null) {
            $weightedSum += 3 * $devoirAvg;
        } else {
            $divisor -= 3;
        }

        foreach ([1, 2, 3] as $term) {
            if ($comp[$term] !== null) {
                $weightedSum += $term * $comp[$term];
            } else {
                $divisor -= $term;
            }
        }

        if ($divisor <= 0) {
            return null;
        }

        return round($weightedSum / $divisor, 2);
    }

    // المعدل السنوي العام للطالب (كل المواد مجتمعة، بضارب كل مادة)
    public function annualAverage(Enrollment $enrollment): ?float
    {
        $subjects = $this->applicableSubjects($enrollment);

        $weightedSum = 0;
        $weightSum = 0;

        foreach ($subjects as $subject) {
            $average = $this->subjectYearAverage($enrollment, $subject);

            if ($average !== null) {
                $weightedSum += $average * $subject->coefficient;
                $weightSum += $subject->coefficient;
            }
        }

        return $weightSum > 0 ? round($weightedSum / $weightSum, 2) : null;
    }

    // "القرار" (نجاح / رسوب) حسب المعدل السنوي العام
    public function decision(?float $average): string
    {
        if ($average === null || $average < 6.5) {
            return '-';
        }

        return $average < 9 ? __('messages.pdf_mention_failed') : __('messages.pdf_mention_passed');
    }

    // "الميزة" السنوية حسب المعدل
    public function annualMention(?float $average): string
    {
        if ($average === null || $average < 10) {
            return '-';
        }

        if ($average < 12) return __('messages.pdf_mention_acceptable');
        if ($average < 14) return __('messages.pdf_mention_satisfactory');
        if ($average < 16) return __('messages.pdf_mention_good');

        return __('messages.pdf_mention_excellent');
    }

    // معدل التأليف/الامتحان لفصل دراسي معين (كل المواد مجتمعة، بضارب كل مادة) — يُستخدم للتقدير الفصلي
    public function termCompositionAverage(Enrollment $enrollment, int $term): ?float
    {
        $subjects = $this->applicableSubjects($enrollment);

        $weightedSum = 0;
        $weightSum = 0;

        foreach ($subjects as $subject) {
            $grade = Grade::whereHas('assessment', function ($q) use ($subject, $term) {
                $q->where('subject_id', $subject->id)
                    ->where('term', $term)
                    ->where('type', 'exam');
            })
                ->where('enrollment_id', $enrollment->id)
                ->where('is_absent', false)
                ->whereNotNull('score')
                ->first();

            if ($grade) {
                $weightedSum += $grade->score * $subject->coefficient;
                $weightSum += $subject->coefficient;
            }
        }

        return $weightSum > 0 ? round($weightedSum / $weightSum, 2) : null;
    }

    // "التقدير" الفصلي حسب معدل التأليف/الامتحان لذاك الفصل
    public function termMention(?float $termCompositionAverage): string
    {
        if ($termCompositionAverage === null) {
            return '-';
        }

        if ($termCompositionAverage < 7) return __('messages.pdf_mention_reprimand');
        if ($termCompositionAverage < 8) return __('messages.pdf_mention_warning');
        if ($termCompositionAverage < 11) return '-';
        if ($termCompositionAverage < 13) return __('messages.pdf_mention_honor_roll');
        if ($termCompositionAverage < 15) return __('messages.pdf_mention_encouragement');

        return __('messages.pdf_mention_felicitations');
    }

    // كل علامات الاختبارات (كل وحدة لحالها) لمادة معينة بفصل دراسي معين
    public function testScoresForTerm(Enrollment $enrollment, Subject $subject, int $term)
    {
        return Grade::with('assessment')
            ->where('enrollment_id', $enrollment->id)
            ->whereHas('assessment', function ($q) use ($subject, $term) {
                $q->where('subject_id', $subject->id)->where('type', 'test')->where('term', $term);
            })
            ->where('is_absent', false)
            ->orderBy('assessment_id')
            ->get();
    }

    // معدل الاختبارات المتراكم لمادة معينة، من الفصل الأول لحد فصل معين
    public function cumulativeTestsAverage(Enrollment $enrollment, Subject $subject, int $uptoTerm): ?float
    {
        $scores = Grade::whereHas('assessment', function ($q) use ($subject, $uptoTerm) {
            $q->where('subject_id', $subject->id)->where('type', 'test')->where('term', '<=', $uptoTerm);
        })
            ->where('enrollment_id', $enrollment->id)
            ->where('is_absent', false)
            ->whereNotNull('score')
            ->pluck('score');

        return $scores->count() > 0 ? round($scores->avg(), 2) : null;
    }

    // علامة التأليف/الامتحان لمادة معينة بفصل دراسي معين
    public function compositionScore(Enrollment $enrollment, Subject $subject, int $term): ?float
    {
        $grade = Grade::whereHas('assessment', function ($q) use ($subject, $term) {
            $q->where('subject_id', $subject->id)->where('type', 'exam')->where('term', $term);
        })
            ->where('enrollment_id', $enrollment->id)
            ->where('is_absent', false)
            ->whereNotNull('score')
            ->first();

        return $grade ? (float) $grade->score : null;
    }

    // متوسط مرجّح بضارب المادة عبر كل المواد، حسب أي قيمة تحددها له (اختبارات، تأليف...)
    public function weightedAcrossSubjects(Enrollment $enrollment, callable $valueFn): ?float
    {
        $subjects = $this->applicableSubjects($enrollment);

        $weightedSum = 0;
        $weightSum = 0;

        foreach ($subjects as $subject) {
            $value = $valueFn($subject);

            if ($value !== null) {
                $weightedSum += $value * $subject->coefficient;
                $weightSum += $subject->coefficient;
            }
        }

        return $weightSum > 0 ? round($weightedSum / $weightSum, 2) : null;
    }

    // معدل عام مبسط (متوسط بسيط لعدة عناصر متوفرة) — يُستخدم لكشوف الفصول (مو السنوي)
    public function simpleAverageOf(array $values): ?float
    {
        $filtered = array_filter($values, fn ($v) => $v !== null);

        return count($filtered) > 0 ? round(array_sum($filtered) / count($filtered), 2) : null;
    }
}