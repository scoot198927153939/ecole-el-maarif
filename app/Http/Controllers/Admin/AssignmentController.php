<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassSubjectTeacher;
use App\Models\Subject;
use App\Models\Teacher;
use App\Support\TeacherAccess;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = ClassSubjectTeacher::with(['classRoom', 'subject', 'teacher'])
            ->get()
            ->sortBy('classRoom.name');

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $classes = ClassRoom::with('academicYear')->orderBy('name')->get();
        $teachers = Teacher::orderBy('first_name')->get();

        return view('admin.assignments.create', compact('classes', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $exists = ClassSubjectTeacher::where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['subject_id' => __('messages.flash_assignment_duplicate_error')])->withInput();
        }

        ClassSubjectTeacher::create($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', __('messages.flash_assignment_created'));
    }

    public function edit(ClassSubjectTeacher $assignment)
    {
        $classes = ClassRoom::with('academicYear')->orderBy('name')->get();
        $teachers = Teacher::orderBy('first_name')->get();

        return view('admin.assignments.edit', compact('assignment', 'classes', 'teachers'));
    }

    public function update(Request $request, ClassSubjectTeacher $assignment)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $assignment->update($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', __('messages.flash_assignment_updated'));
    }

    public function destroy(ClassSubjectTeacher $assignment)
    {
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', __('messages.flash_assignment_deleted'));
    }

    // إرجاع المواد المطبّقة على قسم معين (عبر AJAX) — نفس مواد /gradebook بضارب أكبر من صفر
    public function subjectsForClass(ClassRoom $class)
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

        $subjects = $subjects->orderBy('name')->get(['id', 'name']);

        return response()->json($subjects);
    }
}