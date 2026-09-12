<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('grade_level')->orderBy('name')->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:subjects,code',
            'grade_level' => 'required|string|max:255',
            'coefficient' => 'required|numeric|min:0',
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', __('messages.flash_subject_created'));
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:subjects,code,'.$subject->id,
            'grade_level' => 'required|string|max:255',
            'coefficient' => 'required|numeric|min:0',
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', __('messages.flash_subject_updated'));
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', __('messages.flash_subject_deleted'));
    }
}