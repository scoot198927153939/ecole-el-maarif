<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::orderBy('first_name')->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'employee_number' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'specialization' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'salary_type' => 'required|in:fixed,hourly',
            'fixed_salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'is_partner' => 'nullable|boolean',
        ]);

        $validated['is_partner'] = $request->boolean('is_partner');

        $teacher->update($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', __('messages.flash_teacher_updated'));
    }
}