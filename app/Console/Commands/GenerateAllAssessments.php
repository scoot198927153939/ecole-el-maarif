<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Console\Command;

class GenerateAllAssessments extends Command
{
    protected $signature = 'assessments:generate-all {class_id} {tests_per_term=3}';

    protected $description = 'ينشئ تلقائياً كل الاختبارات والامتحانات لكل مواد شعبة معينة';

    public function handle(): int
    {
        $classId = $this->argument('class_id');
        $testsPerTerm = (int) $this->argument('tests_per_term');

        $class = ClassRoom::find($classId);

        if (! $class) {
            $this->error('الشعبة غير موجودة.');
            return 1;
        }

        $level = preg_replace('/\d+$/', '', $class->name);

        $subjects = Subject::where('grade_level', $level)
            ->where('coefficient', '>', 0)
            ->get();

        if ($subjects->isEmpty()) {
            $this->warn('لا توجد مواد لهذا المستوى ('.$level.').');
            return 0;
        }

        $totalCreated = 0;
        $adminId = auth()->id() ?? \App\Models\User::where('role', 'admin')->first()?->id;

        foreach ($subjects as $subject) {
            // نتجاوز المادة لو أصلاً فيها اختبارات مسجلة (نتجنب التكرار)
            $exists = Assessment::where('class_id', $class->id)
                ->where('subject_id', $subject->id)
                ->exists();

            if ($exists) {
                $this->line('تخطي: '.$subject->name.' (موجودة أصلاً)');
                continue;
            }

            foreach ([1, 2, 3] as $term) {
                $termLabel = $term === 1 ? 'الأول' : ($term === 2 ? 'الثاني' : 'الثالث');

                for ($i = 1; $i <= $testsPerTerm; $i++) {
                    Assessment::create([
                        'title' => 'اختبار '.$i.' - الفصل '.$termLabel,
                        'type' => 'test',
                        'term' => $term,
                        'subject_id' => $subject->id,
                        'class_id' => $class->id,
                        'academic_year_id' => $class->academic_year_id,
                        'coefficient' => 1,
                        'created_by' => $adminId,
                    ]);
                    $totalCreated++;
                }

                Assessment::create([
                    'title' => 'امتحان الفصل '.$termLabel,
                    'type' => 'exam',
                    'term' => $term,
                    'subject_id' => $subject->id,
                    'class_id' => $class->id,
                    'academic_year_id' => $class->academic_year_id,
                    'coefficient' => $term,
                    'created_by' => $adminId,
                ]);
                $totalCreated++;
            }

            $this->info('تم: '.$subject->name);
        }

        $this->info('انتهى! تم إنشاء '.$totalCreated.' اختبار/امتحان لشعبة '.$class->name);

        return 0;
    }
}