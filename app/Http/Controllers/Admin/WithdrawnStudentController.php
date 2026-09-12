<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\MoneySource;
use App\Models\MoneyTransaction;
use App\Models\Student;
use App\Models\WithdrawalRefund;
use Illuminate\Http\Request;

class WithdrawnStudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['guardian', 'enrollments' => function ($q) {
            $q->orderByDesc('academic_year_id');
        }, 'enrollments.classRoom', 'enrollments.withdrawalRefunds'])
            ->where('status', 'withdrawn')
            ->orderBy('first_name')
            ->get();

        return view('admin.withdrawn-students.index', compact('students'));
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student.guardian', 'classRoom', 'feePayments', 'withdrawalRefunds']);
        $moneySources = MoneySource::all();

        return view('admin.withdrawn-students.show', compact('enrollment', 'moneySources'));
    }

    public function storeRefund(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'refund_amount' => 'required|numeric|min:0.01',
            'refund_date' => 'required|date',
            'method' => 'required|in:cash,transfer',
            'money_source_id' => 'required|exists:money_sources,id',
            'transfer_service' => 'required_if:method,transfer|nullable|string|max:255',
            'transfer_number' => 'required_if:method,transfer|nullable|string|max:255',
            'sender_account' => 'required_if:method,transfer|nullable|string|max:255',
            'receiver_account' => 'required_if:method,transfer|nullable|string|max:255',
            'transfer_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'note' => 'nullable|string|max:1000',
        ]);

        $photoPath = null;
        if ($request->hasFile('transfer_photo')) {
            $photoPath = $request->file('transfer_photo')->store('withdrawal_refund_photos', 'public');
        }

        $refund = WithdrawalRefund::create([
            'enrollment_id' => $enrollment->id,
            'refund_amount' => $validated['refund_amount'],
            'refund_date' => $validated['refund_date'],
            'method' => $validated['method'],
            'transfer_service' => $validated['transfer_service'] ?? null,
            'transfer_number' => $validated['transfer_number'] ?? null,
            'sender_account' => $validated['sender_account'] ?? null,
            'receiver_account' => $validated['receiver_account'] ?? null,
            'transfer_photo_path' => $photoPath,
            'money_source_id' => $validated['money_source_id'],
            'note' => $validated['note'] ?? null,
            'recorded_by' => auth()->id(),
        ]);

        MoneyTransaction::create([
            'money_source_id' => $validated['money_source_id'],
            'direction' => 'out',
            'amount' => $validated['refund_amount'],
            'description' => __('messages.flash_withdrawal_refund_description_template', [
                'student' => $enrollment->student->first_name.' '.$enrollment->student->last_name,
                'class' => $enrollment->classRoom->name,
                'number' => $enrollment->student->student_number,
            ]),
            'transaction_date' => $validated['refund_date'],
            'document_path' => $photoPath,
            'recorded_by' => auth()->id(),
        ]);

        return redirect()->route('admin.withdrawn-students.show', $enrollment)
            ->with('success', __('messages.flash_withdrawal_refund_created'));
    }
}