<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoneySource;
use App\Models\MoneyTransaction;
use App\Models\StaffAdvance;
use App\Models\StaffMember;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $moneySources = MoneySource::all();

        return view('admin.staff-advances.create', compact('teachers', 'staffMembers', 'moneySources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_type' => 'required|in:teacher,staff_member',
            'staff_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'date_given' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'money_source_id' => 'required|exists:money_sources,id',
        ]);

        $person = $validated['staff_type'] === 'teacher'
            ? Teacher::findOrFail($validated['staff_id'])
            : StaffMember::findOrFail($validated['staff_id']);

        DB::transaction(function () use ($validated, $person) {
            $transaction = MoneyTransaction::create([
                'money_source_id' => $validated['money_source_id'],
                'direction' => 'out',
                'amount' => $validated['amount'],
                'description' => __('messages.treasury_desc_staff_advance', [], 'ar').': '.$person->first_name.' '.$person->last_name,
                'category' => 'staff_advance',
                'transaction_date' => $validated['date_given'],
                'recorded_by' => auth()->id(),
            ]);

            StaffAdvance::create([
                'staff_type' => $validated['staff_type'],
                'staff_id' => $validated['staff_id'],
                'amount' => $validated['amount'],
                'date_given' => $validated['date_given'],
                'note' => $validated['note'] ?? null,
                'recorded_by' => auth()->id(),
                'money_transaction_id' => $transaction->id,
            ]);
        });

        return redirect()->route('admin.staff-advances.index')
            ->with('success', __('messages.flash_staff_advance_created'));
    }

    public function destroy(StaffAdvance $staffAdvance)
    {
        DB::transaction(function () use ($staffAdvance) {
            $staffAdvance->moneyTransaction?->delete();
            $staffAdvance->delete();
        });

        return redirect()->route('admin.staff-advances.index')
            ->with('success', __('messages.flash_staff_advance_deleted'));
    }
}