<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffAdvance;
use App\Models\StaffAdvanceDeduction;
use App\Models\StaffMember;
use App\Models\Teacher;
use App\Models\TeacherAttendanceSession;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
   public function index(Request $request)
{
    $year = (int) $request->get('year', now()->year);
    $month = (int) $request->get('month', now()->month);

    $partnerTeacherIds = Teacher::where('is_partner', true)->pluck('id');

    $choicesThisMonth = \App\Models\TeacherPaymentChoice::whereIn('teacher_id', $partnerTeacherIds)
        ->where('year', $year)
        ->where('month', $month)
        ->get()
        ->keyBy('teacher_id');

    $teachers = Teacher::where('status', 'active')
        ->get()
        ->filter(function ($teacher) use ($choicesThisMonth) {
            if (! $teacher->is_partner) {
                return true;
            }

            $choice = $choicesThisMonth->get($teacher->id);
            return $choice && $choice->choice === 'salary';
        })
        ->map(function ($teacher) use ($year, $month) {
            return [
                'type_key' => 'teacher',
                'id' => $teacher->id,
                'name' => $teacher->first_name.' '.$teacher->last_name,
                'type' => __('messages.users_role_teacher'),
                'salary_type' => $teacher->salary_type,
                'hours' => $teacher->salary_type === 'hourly' ? $teacher->hoursInMonth($year, $month) : null,
                'amount' => $teacher->calculatedSalary($year, $month),
            ];
        })
        ->values();

    $staffMembers = StaffMember::where('status', 'active')->get()->map(function ($staff) use ($year, $month) {
        return [
            'type_key' => 'staff_member',
            'id' => $staff->id,
            'name' => $staff->first_name.' '.$staff->last_name,
            'type' => $staff->roleLabel(),
            'salary_type' => $staff->salary_type,
            'hours' => $staff->salary_type === 'hourly' ? $staff->hoursInMonth($year, $month) : null,
            'amount' => $staff->calculatedSalary($year, $month),
        ];
    });

    $allPayroll = $teachers->concat($staffMembers);
    $totalPayroll = $allPayroll->sum('amount');

    $partnerTeachers = Teacher::where('is_partner', true)->where('status', 'active')->get();

    return view('admin.payroll.index', compact('allPayroll', 'totalPayroll', 'year', 'month', 'partnerTeachers', 'choicesThisMonth'));
}

public function storePaymentChoice(Request $request)
{
    $validated = $request->validate([
        'teacher_id' => 'required|exists:teachers,id',
        'year' => 'required|integer',
        'month' => 'required|integer|between:1,12',
        'choice' => 'required|in:salary,partner_share',
    ]);

    \App\Models\TeacherPaymentChoice::updateOrCreate(
        ['teacher_id' => $validated['teacher_id'], 'year' => $validated['year'], 'month' => $validated['month']],
        ['choice' => $validated['choice'], 'recorded_by' => auth()->id()]
    );

    return back()->with('success', __('messages.flash_payroll_choice_updated'));
}

    public function show(Request $request, string $type, int $id)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $person = $type === 'teacher' ? Teacher::findOrFail($id) : StaffMember::findOrFail($id);

        $grossSalary = $person->calculatedSalary($year, $month);
        $hoursWorked = $person->salary_type === 'hourly' ? $person->hoursInMonth($year, $month) : null;

        $lateMinutes = 0;
        $scheduledSessionsPerWeek = null;
        $scheduledHoursThisMonth = null;

        if ($type === 'teacher') {
            $sessions = TeacherAttendanceSession::where('teacher_id', $id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $lateMinutes = $sessions->sum(fn ($s) => $s->late_minutes);

            $scheduledSessionsPerWeek = $person->scheduledSessionsPerWeek();
            $scheduledHoursThisMonth = $scheduledSessionsPerWeek * 4 * 2;
        }

        $advances = StaffAdvance::where('staff_type', $type)
            ->where('staff_id', $id)
            ->with('deductions')
            ->get();

        $totalAdvanceTaken = $advances->sum('amount');
        $totalAdvanceRemaining = $advances->sum('remaining_balance');

        $thisMonthDeduction = StaffAdvanceDeduction::whereIn('staff_advance_id', $advances->pluck('id'))
            ->where('year', $year)
            ->where('month', $month)
            ->sum('amount');

        $netPayable = $grossSalary - $thisMonthDeduction;

        return view('admin.payroll.show', compact(
            'person', 'type', 'year', 'month', 'grossSalary', 'hoursWorked', 'lateMinutes',
            'scheduledSessionsPerWeek', 'scheduledHoursThisMonth',
            'advances', 'totalAdvanceTaken', 'totalAdvanceRemaining', 'thisMonthDeduction', 'netPayable'
        ));
    }

    public function storeDeduction(Request $request, StaffAdvance $staffAdvance)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        if ($validated['amount'] > $staffAdvance->remaining_balance) {
            return back()->withErrors(['amount' => __('messages.flash_payroll_deduction_exceeds_error')]);
        }

        StaffAdvanceDeduction::updateOrCreate(
            ['staff_advance_id' => $staffAdvance->id, 'year' => $validated['year'], 'month' => $validated['month']],
            ['amount' => $validated['amount'], 'recorded_by' => auth()->id()]
        );

        return back()->with('success', __('messages.flash_payroll_deduction_created'));
    }
}