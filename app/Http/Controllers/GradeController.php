<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Notification;
use App\Support\TeacherAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index(Assessment $assessment)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($assessment->class_id, $assessment->subject_id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $enrollments = Enrollment::where('class_id', $assessment->class_id)
            ->with('student')
            ->get()
            ->sortBy(fn ($e) => $e->student->first_name . ' ' . $e->student->last_name)
            ->values();

        $grades = Grade::where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('enrollment_id');

        return view('grades.index', compact('assessment', 'enrollments', 'grades'));
    }

    public function store(Request $request, Assessment $assessment)
    {
        if (TeacherAccess::isRestricted() && ! TeacherAccess::owns($assessment->class_id, $assessment->subject_id)) {
            abort(403, TeacherAccess::deniedMessage());
        }

        $validated = $request->validate([
            'scores' => 'nullable|array',
            'scores.*' => 'nullable|numeric|min:0|max:20',
        ]);

        $scores = $validated['scores'] ?? [];
        $absent = $request->input('absent', []);
        $notes = $request->input('notes', []);

        $enrollmentIds = array_unique(array_merge(
            array_keys($scores),
            array_keys($absent),
            array_keys($notes)
        ));

        foreach ($enrollmentIds as $enrollmentId) {
            $isAbsent = isset($absent[$enrollmentId]);
            $score = $scores[$enrollmentId] ?? null;
            $note = $notes[$enrollmentId] ?? null;

            if ($score === null && !$isAbsent && $note === null) {
                continue;
            }

            $newScore = $isAbsent ? null : $score;

            $existing = Grade::where('enrollment_id', $enrollmentId)
                ->where('assessment_id', $assessment->id)
                ->first();

            if ($existing) {
                if (!is_null($existing->score) && (float) $existing->score !== ($newScore !== null ? (float) $newScore : null)) {
                    Notification::create([
                        'grade_id'   => $existing->id,
                        'changed_by' => Auth::id(),
                        'old_score'  => $existing->score,
                        'new_score'  => $newScore,
                        'message'    => 'تم تعديل علامة عبر صفحة إدخال العلامات',
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
                    'subject_id'    => $assessment->subject_id,
                    'assessment_id' => $assessment->id,
                    'score'         => $newScore,
                    'is_absent'     => $isAbsent,
                    'teacher_note'  => $note,
                    'entered_by'    => Auth::id(),
                ]);
            }
        }

        return redirect()->route('grades.index', $assessment)
            ->with('success', __('messages.flash_grades_saved'));
    }
}