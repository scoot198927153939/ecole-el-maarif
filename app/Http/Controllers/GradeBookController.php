<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Notification;
use App\Support\TeacherAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeBookController extends Controller
{
    public function classes()
    {
        $classes = ClassRoom::with('academicYear')->orderBy('name');

        if (TeacherAccess::isRestricted()) {
            $classes->whereIn('id', TeacherAccess::assignedClassIds());
        }

        $classes = $classes->get();

        return view('gradebook.classes', compact('classes'));
    }

    public function subjects(ClassRoom $class)
    {
        if (TeacherAccess::isRestricted() && ! in_array($class->id, TeacherAccess::assignedClassIds(), true)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $level = preg_replace('/\d+$/', '', $class->name);

        $subjects = Subject::where('grade_level', $level)
            ->where('coefficient', '>', 0);

        if (TeacherAccess::isRestricted()) {
            $subjects->whereIn('id', TeacherAccess::assignedSubjectIds($class->id));
        }

        $subjects = $subjects->orderBy('name')->get();

        return view('gradebook.subjects', compact('class', 'subjects'));
    }

    public function grid(ClassRoom $class, Subject $subject)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($class->id, $subject->id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $assessments = Assessment::where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->orderBy('term')
            ->orderBy('assessment_date')
            ->get();

        $enrollments = Enrollment::where('class_id', $class->id)
            ->with('student')
            ->get()
            ->sortBy(fn ($e) => $e->student->first_name . ' ' . $e->student->last_name)
            ->values();

        $grades = Grade::whereIn('assessment_id', $assessments->pluck('id'))
            ->get()
            ->groupBy('enrollment_id');

        return view('gradebook.grid', compact('class', 'subject', 'assessments', 'enrollments', 'grades'));
    }

    public function store(Request $request, ClassRoom $class, Subject $subject)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($class->id, $subject->id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $validated = $request->validate([
            'scores' => 'nullable|array',
            'scores.*.*' => 'nullable|numeric|min:0|max:20',
        ]);

        $scores = $validated['scores'] ?? [];
        $absent = $request->input('absent', []);
        $notes = $request->input('notes', []);

        foreach ($scores as $enrollmentId => $assessmentScores) {
            foreach ($assessmentScores as $assessmentId => $score) {
                $isAbsent = isset($absent[$enrollmentId][$assessmentId]);
                $note = $notes[$enrollmentId][$assessmentId] ?? null;

                if ($score === null && !$isAbsent) {
                    continue;
                }

                $existing = Grade::where('enrollment_id', $enrollmentId)
                    ->where('assessment_id', $assessmentId)
                    ->first();

                if ($existing) {
                    $newScore = $isAbsent ? null : $score;

                    if (!is_null($existing->score) && (float) $existing->score !== ($newScore !== null ? (float) $newScore : null)) {
                        Notification::create([
                            'grade_id'   => $existing->id,
                            'changed_by' => Auth::id(),
                            'old_score'  => $existing->score,
                            'new_score'  => $newScore,
                            'message'    => 'تم تعديل علامة عبر دفتر العلامات',
                        ]);
                    }

                    $existing->update([
                        'score'        => $newScore,
                        'is_absent'    => $isAbsent,
                        'teacher_note' => $note,
                        'entered_by'   => Auth::id(),
                    ]);
                } else {
                    Grade::create([
                        'enrollment_id' => $enrollmentId,
                        'subject_id'    => $subject->id,
                        'assessment_id' => $assessmentId,
                        'score'         => $isAbsent ? null : $score,
                        'is_absent'     => $isAbsent,
                        'teacher_note'  => $note,
                        'entered_by'    => Auth::id(),
                    ]);
                }
            }
        }

        return redirect()->route('gradebook.grid', [$class, $subject])
            ->with('success', __('messages.flash_gradebook_saved'));
    }
}