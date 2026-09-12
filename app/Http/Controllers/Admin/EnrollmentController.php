<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'classRoom', 'academicYear'])
            ->orderBy('enrollment_date', 'desc')
            ->get();

        return view('admin.enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        $students = Student::orderBy('first_name')->get();
        $classes = ClassRoom::with('academicYear')->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.enrollments.create', compact('students', 'classes', 'academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_status' => 'required|in:active,transferred,completed',
            'enrollment_date' => 'required|date',
        ]);

        Enrollment::create($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', __('messages.flash_enrollment_created'));
    }

    public function edit(Enrollment $enrollment)
    {
        $students = Student::orderBy('first_name')->get();
        $classes = ClassRoom::with('academicYear')->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.enrollments.edit', compact('enrollment', 'students', 'classes', 'academicYears'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_status' => 'required|in:active,transferred,completed',
            'enrollment_date' => 'required|date',
        ]);

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', __('messages.flash_enrollment_updated'));
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
            ->with('success', __('messages.flash_enrollment_deleted'));
    }
}