<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    // عرض قائمة كل السنوات الدراسية
    public function index()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.academic_years.index', compact('academicYears'));
    }

    // عرض نموذج إضافة سنة دراسية جديدة
    public function create()
    {
        return view('admin.academic_years.create');
    }

    // حفظ سنة دراسية جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        $validated['is_current'] = $request->has('is_current');

        // إذا كانت هذه السنة "حالية"، نلغي هذه الصفة عن باقي السنوات
        if ($validated['is_current']) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        AcademicYear::create($validated);

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('messages.flash_academic_year_created'));
    }

    // عرض نموذج تعديل سنة دراسية
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic_years.edit', compact('academicYear'));
    }

    // تحديث سنة دراسية
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        $validated['is_current'] = $request->has('is_current');

        if ($validated['is_current']) {
            AcademicYear::where('id', '!=', $academicYear->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        $academicYear->update($validated);

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('messages.flash_academic_year_updated'));
    }

    // حذف سنة دراسية
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('messages.flash_academic_year_deleted'));
    }
}