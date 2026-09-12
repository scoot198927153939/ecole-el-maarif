<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjectNames = [
            'ar' => 'اللغة العربية',
            'fr' => 'اللغة الفرنسية',
            'an' => 'اللغة الإنجليزية',
            'math' => 'الرياضيات',
            'pc' => 'الفيزياء والكيمياء',
            'sn' => 'علوم الحياة والأرض',
            'ir' => 'التربية الإسلامية',
            'ic' => 'القرآن الكريم',
            'hg' => 'التاريخ والجغرافيا',
            'philo' => 'الفلسفة',
        ];

        $levels = [
            '1AS' => ['ar'=>5,'fr'=>4,'an'=>1,'math'=>5,'pc'=>1,'sn'=>2,'ir'=>3,'ic'=>1,'hg'=>2],
            '2AS' => ['ar'=>5,'fr'=>4,'an'=>1,'math'=>5,'pc'=>0,'sn'=>2,'ir'=>3,'ic'=>1,'hg'=>2],
            '3AS' => ['ar'=>5,'fr'=>3,'an'=>1,'math'=>5,'pc'=>2,'sn'=>2,'ir'=>3,'ic'=>1,'hg'=>2],
            '4AS' => ['ar'=>5,'fr'=>3,'an'=>1,'math'=>5,'pc'=>2,'sn'=>2,'ir'=>3,'ic'=>1,'hg'=>2],
            '5MA' => ['ar'=>3,'fr'=>3,'an'=>2,'math'=>6,'pc'=>5,'sn'=>3,'ir'=>2,'ic'=>0,'hg'=>2,'philo'=>2],
            '5SN' => ['ar'=>3,'fr'=>3,'an'=>2,'math'=>4,'pc'=>4,'sn'=>6,'ir'=>2,'ic'=>0,'hg'=>2,'philo'=>2],
            '6MA' => ['ar'=>2,'fr'=>2,'an'=>2,'math'=>7,'pc'=>6,'sn'=>3,'ir'=>2,'ic'=>0,'hg'=>2,'philo'=>2],
            '6SN' => ['ar'=>2,'fr'=>2,'an'=>2,'math'=>4,'pc'=>6,'sn'=>6,'ir'=>2,'ic'=>0,'hg'=>2,'philo'=>2],
            '7MA' => ['ar'=>3,'fr'=>3,'an'=>2,'math'=>9,'pc'=>8,'sn'=>4,'ir'=>2],
            '7SN' => ['ar'=>3,'fr'=>3,'an'=>2,'math'=>6,'pc'=>7,'sn'=>8,'ir'=>2],
            '7LO' => ['ar'=>6,'fr'=>2,'an'=>0,'math'=>2,'pc'=>0,'sn'=>2,'ir'=>6,'ic'=>4,'hg'=>3,'philo'=>4],
        ];

        foreach ($levels as $level => $subjects) {
            foreach ($subjects as $code => $coefficient) {
                Subject::updateOrCreate(
                    ['code' => strtoupper($code).'_'.$level],
                    [
                        'name' => $subjectNames[$code],
                        'grade_level' => $level,
                        'coefficient' => $coefficient,
                    ]
                );
            }
        }
    }
}