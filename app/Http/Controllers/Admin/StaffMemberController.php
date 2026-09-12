<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use Illuminate\Http\Request;

class StaffMemberController extends Controller
{
    public function index()
    {
        $staffMembers = StaffMember::orderBy('first_name')->get();

        return view('admin.staff-members.index', compact('staffMembers'));
    }

    public function create()
    {
        return view('admin.staff-members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'required|in:director,supervisor,accountant,cleaner,guard',
            'phone' => 'nullable|string|max:50',
            'salary_type' => 'required|in:hourly,fixed',
            'fixed_salary' => 'required_if:salary_type,fixed|nullable|numeric|min:0',
            'hourly_rate' => 'required_if:salary_type,hourly|nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        StaffMember::create($validated);

        return redirect()->route('admin.staff-members.index')
            ->with('success', __('messages.flash_staff_member_created'));
    }

    public function edit(StaffMember $staffMember)
    {
        return view('admin.staff-members.edit', compact('staffMember'));
    }

    public function update(Request $request, StaffMember $staffMember)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'required|in:director,supervisor,accountant,cleaner,guard',
            'phone' => 'nullable|string|max:50',
            'salary_type' => 'required|in:hourly,fixed',
            'fixed_salary' => 'required_if:salary_type,fixed|nullable|numeric|min:0',
            'hourly_rate' => 'required_if:salary_type,hourly|nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $staffMember->update($validated);

        return redirect()->route('admin.staff-members.index')
            ->with('success', __('messages.flash_staff_member_updated'));
    }

    public function destroy(StaffMember $staffMember)
    {
        $staffMember->delete();

        return redirect()->route('admin.staff-members.index')
            ->with('success', __('messages.flash_staff_member_deleted'));
    }
}