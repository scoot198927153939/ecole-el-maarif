<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffAdvance;
use App\Models\StaffMember;
use App\Models\Teacher;
use Illuminate\Http\Request;

class StaffAdvanceController extends Controller
{
    public function index()
    {
        $advances = StaffAdvance::with('deductions')->orderByDesc('date_given')->get();

        $advances->each(function ($advance) {
            $advance->staff_name = optional($advance->staffMember())->first_name.' '.optional($advance->staffMember())->last_name;
        });

        return view('admin.staff-advances.index', compact('advances'));
    }

    public function create()
    {
        $teachers = Teacher::where('status', 'active')->orderBy('first_name')->get();
        $staffMembers = StaffMember::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.staff-advances.create', compact('teachers', 'staffMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_type' => 'required|in:teacher,staff_member',
            'staff_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'date_given' => 'required|date',
            'note' => 'nullable|string|max:1000',
        ]);

        StaffAdvance::create([
            ...$validated,
            'recorded_by' => auth()->id(),
        ]);

        return redirect()->route('admin.staff-advances.index')
            ->with('success', __('messages.flash_staff_advance_created'));
    }

    public function destroy(StaffAdvance $staffAdvance)
    {
        $staffAdvance->delete();

        return redirect()->route('admin.staff-advances.index')
            ->with('success', __('messages.flash_staff_advance_deleted'));
    }
}