<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::with('academicYear')->orderBy('name')->get();
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.classes.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        ClassRoom::create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', __('messages.flash_class_created'));
    }

    public function edit(ClassRoom $class)
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.classes.edit', compact('class', 'academicYears'));
    }

    public function update(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $class->update($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', __('messages.flash_class_updated'));
    }

    public function destroy(ClassRoom $class)
    {
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', __('messages.flash_class_deleted'));
    }
}