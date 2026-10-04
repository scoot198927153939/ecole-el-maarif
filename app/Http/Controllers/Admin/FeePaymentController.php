<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\FeePayment;
use App\Models\MoneySource;
use App\Models\MoneyTransaction;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'classRoom', 'academicYear'])
            ->orderBy('enrollment_date', 'desc')
            ->get();

        return view('admin.fee-payments.index', compact('enrollments'));
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'classRoom', 'academicYear', 'feePayments' => function ($q) {
            $q->orderBy('payment_date', 'desc');
        }]);

        $moneySources = MoneySource::all();

        return view('admin.fee-payments.show', compact('enrollment', 'moneySources'));
    }

    public function store(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'required|in:cash,transfer',
            'money_source_id' => 'required|exists:money_sources,id',
            'transfer_service' => 'required_if:method,transfer|nullable|string|max:255',
            'transfer_number' => 'required_if:method,transfer|nullable|string|max:255',
            'sender_account' => 'required_if:method,transfer|nullable|string|max:255',
            'receiver_account' => 'required_if:method,transfer|nullable|string|max:255',
            'transfer_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'next_payment_due_date' => 'nullable|date',
        ]);

        $photoPath = null;
        if ($request->hasFile('transfer_photo')) {
            $photoPath = $request->file('transfer_photo')->store('fee_payment_photos', 'public');
        }

        FeePayment::create([
            'enrollment_id' => $enrollment->id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'method' => $validated['method'],
            'transfer_service' => $validated['transfer_service'] ?? null,
            'transfer_number' => $validated['transfer_number'] ?? null,
            'sender_account' => $validated['sender_account'] ?? null,
            'receiver_account' => $validated['receiver_account'] ?? null,
            'transfer_photo_path' => $photoPath,
            'money_source_id' => $validated['money_source_id'],
            'recorded_by' => auth()->id(),
        ]);

        MoneyTransaction::create([
            'money_source_id' => $validated['money_source_id'],
            'direction' => 'in',
            'amount' => $validated['amount'],
            'description' => __('messages.flash_fee_payment_description_template', [
                'student' => $enrollment->student->first_name.' '.$enrollment->student->last_name,
                'class' => $enrollment->classRoom->name,
                'number' => $enrollment->student->student_number,
            ]),
            'category' => 'fee_payment',
            'transaction_date' => $validated['payment_date'],
            'document_path' => $photoPath,
            'recorded_by' => auth()->id(),
        ]);

        // تحديث موعد الدفعة القادمة لو تم تحديده
        if (! empty($validated['next_payment_due_date'])) {
            $enrollment->update(['next_payment_due_date' => $validated['next_payment_due_date']]);
        }

        return redirect()->route('admin.fee-payments.show', $enrollment)
            ->with('success', __('messages.flash_fee_payment_created'));
    }

    public function updateDiscount(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $enrollment->update($validated);

        return redirect()->route('admin.fee-payments.show', $enrollment)
            ->with('success', __('messages.flash_fee_payment_discount_updated'));
    }
}