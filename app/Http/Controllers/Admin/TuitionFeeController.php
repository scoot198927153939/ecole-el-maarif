<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\TuitionFee;
use Illuminate\Http\Request;

class TuitionFeeController extends Controller
{
    public function index()
    {
        $fees = TuitionFee::with('academicYear')->orderBy('grade_level')->get();

        return view('admin.tuition-fees.index', compact('fees'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $gradeLevels = Subject::select('grade_level')->distinct()->orderBy('grade_level')->pluck('grade_level');

        return view('admin.tuition-fees.create', compact('academicYears', 'gradeLevels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_level' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'amount' => 'required|numeric|min:0',
        ]);

        TuitionFee::create($validated);

        return redirect()->route('admin.tuition-fees.index')
            ->with('success', __('messages.flash_tuition_fee_created'));
    }

    public function edit(TuitionFee $tuitionFee)
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $gradeLevels = Subject::select('grade_level')->distinct()->orderBy('grade_level')->pluck('grade_level');

        return view('admin.tuition-fees.edit', compact('tuitionFee', 'academicYears', 'gradeLevels'));
    }

    public function update(Request $request, TuitionFee $tuitionFee)
    {
        $validated = $request->validate([
            'grade_level' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $tuitionFee->update($validated);

        return redirect()->route('admin.tuition-fees.index')
            ->with('success', __('messages.flash_tuition_fee_updated'));
    }

    public function destroy(TuitionFee $tuitionFee)
    {
        $tuitionFee->delete();

        return redirect()->route('admin.tuition-fees.index')
            ->with('success', __('messages.flash_tuition_fee_deleted'));
    }
}