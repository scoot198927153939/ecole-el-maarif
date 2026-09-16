<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Support\TeacherAccess;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    // صفحة اختيار القسم
    public function classesIndex()
    {
        $classes = ClassRoom::with('academicYear')->orderBy('name');

        if (TeacherAccess::isRestricted()) {
            $classes->whereIn('id', TeacherAccess::assignedClassIds());
        }

        $classes = $classes->withCount('assessments')->get();

        return view('assessments.classes-index', compact('classes'));
    }

    // كل الاختبارات والامتحانات لقسم واحد، مجمّعة حسب الفصل الدراسي
    public function forClass(ClassRoom $class)
    {
        if (TeacherAccess::isRestricted() && ! in_array($class->id, TeacherAccess::assignedClassIds(), true)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $assessments = Assessment::with(['subject', 'academicYear', 'creator'])
            ->where('class_id', $class->id);

        if (TeacherAccess::isRestricted()) {
            $assessments->whereIn('subject_id', TeacherAccess::assignedSubjectIds($class->id));
        }

        $assessments = $assessments->orderBy('term')
            ->orderBy('subject_id')
            ->orderBy('assessment_date')
            ->get()
            ->groupBy('term');

        $canBulkAll = ! TeacherAccess::isRestricted();

        return view('assessments.class-show', compact('class', 'assessments', 'canBulkAll'));
    }

    public function create(ClassRoom $class)
    {
        if (TeacherAccess::isRestricted() && ! in_array($class->id, TeacherAccess::assignedClassIds(), true)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $subjects = Subject::orderBy('name')->get();

        if (TeacherAccess::isRestricted()) {
            $subjects = $subjects->whereIn('id', TeacherAccess::assignedSubjectIds($class->id))->values();
        }

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('assessments.create', compact('class', 'subjects', 'academicYears'));
    }

    public function store(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:test,exam',
            'term' => 'required|integer|in:1,2,3',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'coefficient' => 'required|numeric|min:0.1',
            'assessment_date' => 'nullable|date',
        ]);

        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($class->id, (int) $validated['subject_id'])) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $validated['class_id'] = $class->id;
        $validated['created_by'] = auth()->id();

        Assessment::create($validated);

        return redirect()->route('assessments.class.show', $class)
            ->with('success', __('messages.flash_assessment_created'));
    }

    public function edit(Assessment $assessment)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($assessment->class_id, $assessment->subject_id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $subjects = Subject::orderBy('name')->get();

        if (TeacherAccess::isRestricted()) {
            $subjects = $subjects->whereIn('id', TeacherAccess::assignedSubjectIds($assessment->class_id))->values();
        }

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('assessments.edit', compact('assessment', 'subjects', 'academicYears'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($assessment->class_id, $assessment->subject_id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:test,exam',
            'term' => 'required|integer|in:1,2,3',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'coefficient' => 'required|numeric|min:0.1',
            'assessment_date' => 'nullable|date',
        ]);

        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($assessment->class_id, (int) $validated['subject_id'])) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $assessment->update($validated);

        return redirect()->route('assessments.class.show', $assessment->class_id)
            ->with('success', __('messages.flash_assessment_updated'));
    }

    public function destroy(Assessment $assessment)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($assessment->class_id, $assessment->subject_id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $classId = $assessment->class_id;
        $assessment->delete();

        return redirect()->route('assessments.class.show', $classId)
            ->with('success', __('messages.flash_assessment_deleted'));
    }

    // إنشاء اختبارات وامتحانات كل الفصول الثلاثة، لكل مادة مطبّقة على مستوى هذا القسم دفعة واحدة
    public function bulkCreateAll(ClassRoom $class)
    {
        abort_if(TeacherAccess::isRestricted(), 403, TeacherAccess::deniedMessage());

        $level = preg_replace('/\d+$/', '', $class->name);

        $subjects = Subject::where('grade_level', $level)
            ->where('coefficient', '>', 0)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('assessments.bulk-all', compact('class', 'subjects', 'academicYears'));
    }

    public function bulkStoreAll(Request $request, ClassRoom $class)
    {
        abort_if(TeacherAccess::isRestricted(), 403, TeacherAccess::deniedMessage());

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'tests_per_term' => 'required|integer|min:1|max:6',
        ]);

        $level = preg_replace('/\d+$/', '', $class->name);

        $subjects = Subject::where('grade_level', $level)
            ->where('coefficient', '>', 0)
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($subjects as $subject) {
            $alreadyExists = Assessment::where('class_id', $class->id)
                ->where('subject_id', $subject->id)
                ->where('academic_year_id', $validated['academic_year_id'])
                ->exists();

            if ($alreadyExists) {
                $skipped++;

                continue;
            }

            $created += $this->generateTermAssessments(
                $class->id,
                $subject->id,
                (int) $validated['academic_year_id'],
                (int) $validated['tests_per_term']
            );
        }

        return redirect()->route('assessments.class.show', $class)
            ->with('success', __('messages.flash_assessment_bulk_all_created', ['count' => $created, 'skipped' => $skipped]));
    }

    private function generateTermAssessments(int $classId, int $subjectId, int $academicYearId, int $testsPerTerm): int
    {
        $count = 0;

        foreach ([1, 2, 3] as $term) {
            $termLabel = $term === 1 ? 'الأول' : ($term === 2 ? 'الثاني' : 'الثالث');

            for ($i = 1; $i <= $testsPerTerm; $i++) {
                Assessment::create([
                    'title' => 'اختبار '.$i.' - الفصل '.$termLabel,
                    'type' => 'test',
                    'term' => $term,
                    'subject_id' => $subjectId,
                    'class_id' => $classId,
                    'academic_year_id' => $academicYearId,
                    'coefficient' => 1,
                    'created_by' => auth()->id(),
                ]);
                $count++;
            }

            Assessment::create([
                'title' => 'امتحان الفصل '.$termLabel,
                'type' => 'exam',
                'term' => $term,
                'subject_id' => $subjectId,
                'class_id' => $classId,
                'academic_year_id' => $academicYearId,
                'coefficient' => $term,
                'created_by' => auth()->id(),
            ]);
            $count++;
        }

        return $count;
    }
}
