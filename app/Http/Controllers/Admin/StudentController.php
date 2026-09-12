<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['guardian', 'enrollments.classRoom'])
            ->get()
            ->map(function ($student) {
                $student->current_class = $student->enrollments
                    ->sortByDesc('academic_year_id')
                    ->first()?->classRoom;
                return $student;
            })
            ->sort(function ($a, $b) {
                $classA = $a->current_class->name ?? '';
                $classB = $b->current_class->name ?? '';
                return $classA <=> $classB ?: ((int) $a->student_number <=> (int) $b->student_number);
            })
            ->values();

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $guardians = Guardian::orderBy('name')->get();
        $classes = ClassRoom::with('academicYear')->orderBy('name')->get();

        $lastNumber = Student::max('student_number');
        $nextNumber = $lastNumber ? ((int) $lastNumber) + 1 : 1001;

        return view('admin.students.create', compact('guardians', 'classes', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'school_number' => 'nullable|string|max:255',
            'guardian_id' => 'nullable|exists:guardians,id',
            'status' => 'required|in:active,graduated,withdrawn',
            'class_id' => 'required|exists:classes,id',
        ]);

        $lastNumber = Student::max('student_number');
        $nextNumber = $lastNumber ? ((int) $lastNumber) + 1 : 1001;

        $class = ClassRoom::findOrFail($validated['class_id']);

        $student = Student::create([
            'student_number' => $nextNumber,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'birth_date' => $validated['birth_date'],
            'birth_place' => $validated['birth_place'] ?? null,
            'national_id' => $validated['national_id'] ?? null,
            'school_number' => $validated['school_number'] ?? null,
            'guardian_id' => $validated['guardian_id'] ?? null,
            'status' => $validated['status'],
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $class->academic_year_id,
            'enrollment_status' => 'active',
            'enrollment_date' => now(),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', __('messages.flash_student_created', ['number' => $nextNumber]))
            ->with('new_student_id', $student->id);
    }

    public function edit(Student $student)
    {
        $guardians = Guardian::orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'guardians'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'school_number' => 'nullable|string|max:255',
            'guardian_id' => 'nullable|exists:guardians,id',
            'status' => 'required|in:active,graduated,withdrawn',
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', __('messages.flash_student_updated'));
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')
            ->with('success', __('messages.flash_student_deleted'));
    }
}