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
    public function index()
    {
        $assessments = Assessment::with(['subject', 'classRoom', 'academicYear', 'creator']);

        if (TeacherAccess::isRestricted()) {
            $assessments->whereIn('class_id', TeacherAccess::assignedClassIds());
        }

        $assessments = $assessments->orderBy('term')
            ->orderBy('assessment_date', 'desc')
            ->get();

        return view('assessments.index', compact('assessments'));
    }

    public function create()
    {
        $classes = ClassRoom::with('academicYear')->orderBy('name');

        if (TeacherAccess::isRestricted()) {
            $classes->whereIn('id', TeacherAccess::assignedClassIds());
        }

        $classes = $classes->get();

        $subjects = Subject::orderBy('name')->get();

        if (TeacherAccess::isRestricted()) {
            $subjects = $subjects->whereIn('id', TeacherAccess::assignedSubjectIds())->values();
        }

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('assessments.create', compact('subjects', 'classes', 'academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:test,exam',
            'term' => 'required|integer|in:1,2,3',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'coefficient' => 'required|numeric|min:0.1',
            'assessment_date' => 'nullable|date',
        ]);

        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns((int) $validated['class_id'], (int) $validated['subject_id'])) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $validated['created_by'] = auth()->id();

        Assessment::create($validated);

        return redirect()->route('assessments.index')
            ->with('success', __('messages.flash_assessment_created'));
    }

    public function edit(Assessment $assessment)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($assessment->class_id, $assessment->subject_id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $classes = ClassRoom::with('academicYear')->orderBy('name');

        if (TeacherAccess::isRestricted()) {
            $classes->whereIn('id', TeacherAccess::assignedClassIds());
        }

        $classes = $classes->get();

        $subjects = Subject::orderBy('name')->get();

        if (TeacherAccess::isRestricted()) {
            $subjects = $subjects->whereIn('id', TeacherAccess::assignedSubjectIds())->values();
        }

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('assessments.edit', compact('assessment', 'subjects', 'classes', 'academicYears'));
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
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'coefficient' => 'required|numeric|min:0.1',
            'assessment_date' => 'nullable|date',
        ]);

        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns((int) $validated['class_id'], (int) $validated['subject_id'])) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $assessment->update($validated);

        return redirect()->route('assessments.index')
            ->with('success', __('messages.flash_assessment_updated'));
    }

    public function destroy(Assessment $assessment)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($assessment->class_id, $assessment->subject_id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $assessment->delete();

        return redirect()->route('assessments.index')
            ->with('success', __('messages.flash_assessment_deleted'));
    }

    public function bulkCreate()
    {
        $subjects = Subject::where('coefficient', '>', 0)
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        if (TeacherAccess::isRestricted()) {
            $subjects = $subjects->whereIn('id', TeacherAccess::assignedSubjectIds())->values();
        }

        $classes = ClassRoom::with('academicYear')->orderBy('name');

        if (TeacherAccess::isRestricted()) {
            $classes->whereIn('id', TeacherAccess::assignedClassIds());
        }

        $classes = $classes->get();

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $classLevels = $classes->mapWithKeys(function ($class) {
            return [$class->id => preg_replace('/\d+$/', '', $class->name)];
        });

        return view('assessments.bulk-create', compact('subjects', 'classes', 'academicYears', 'classLevels'));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'tests_per_term' => 'required|integer|min:1|max:6',
        ]);

        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns((int) $validated['class_id'], (int) $validated['subject_id'])) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $count = 0;

        foreach ([1, 2, 3] as $term) {
            $termLabel = $term === 1 ? 'الأول' : ($term === 2 ? 'الثاني' : 'الثالث');

            for ($i = 1; $i <= $validated['tests_per_term']; $i++) {
                Assessment::create([
                    'title' => 'اختبار '.$i.' - الفصل '.$termLabel,
                    'type' => 'test',
                    'term' => $term,
                    'subject_id' => $validated['subject_id'],
                    'class_id' => $validated['class_id'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'coefficient' => 1,
                    'created_by' => auth()->id(),
                ]);
                $count++;
            }

            Assessment::create([
                'title' => 'امتحان الفصل '.$termLabel,
                'type' => 'exam',
                'term' => $term,
                'subject_id' => $validated['subject_id'],
                'class_id' => $validated['class_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'coefficient' => $term,
                'created_by' => auth()->id(),
            ]);
            $count++;
        }

        return redirect()->route('assessments.index')
            ->with('success', __('messages.flash_assessment_bulk_created', ['count' => $count]));
    }
}