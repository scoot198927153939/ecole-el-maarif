<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\MoneySource;
use App\Models\MoneyTransaction;
use App\Models\Partner;
use App\Models\PartnerWithdrawal;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    private function netProfitSinceYearStart(AcademicYear $academicYear): float
    {
        $yearStart = $academicYear->start_date ?? $academicYear->created_at;

        $totalIn = MoneyTransaction::where('direction', 'in')
            ->where('transaction_date', '>=', $yearStart)
            ->sum('amount');

        $partnerWithdrawalTransactionIds = PartnerWithdrawal::whereNotNull('money_transaction_id')->pluck('money_transaction_id');

        $totalOut = MoneyTransaction::where('direction', 'out')
            ->whereNotIn('id', $partnerWithdrawalTransactionIds)
            ->where('transaction_date', '>=', $yearStart)
            ->sum('amount');

        return (float) $totalIn - (float) $totalOut;
    }

    public function index()
    {
        $currentYear = AcademicYear::latest('id')->first();
        $netProfit = $currentYear ? $this->netProfitSinceYearStart($currentYear) : 0;

        $partners = Partner::with('withdrawals')
            ->where('academic_year_id', $currentYear?->id)
            ->get()
            ->map(function ($partner) use ($netProfit) {
                $entitled = $netProfit * ((float) $partner->percentage / 100);
                $partner->entitled_amount = $entitled;
                $partner->over_withdrawn = $partner->total_withdrawn > $entitled;
                $partner->difference = $entitled - $partner->total_withdrawn;
                return $partner;
            });

        return view('admin.partners.index', compact('partners', 'netProfit', 'currentYear'));
    }

    public function create()
    {
        $teachers = Teacher::where('status', 'active')->orderBy('first_name')->get();
        $academicYear = AcademicYear::latest('id')->first();

        return view('admin.partners.create', compact('teachers', 'academicYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'nullable|exists:teachers,id',
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0.01|max:100',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        Partner::create($validated + ['status' => 'active']);

        if ($request->filled('teacher_id')) {
            Teacher::where('id', $validated['teacher_id'])->update(['is_partner' => true]);
        }

        return redirect()->route('admin.partners.index')->with('success', __('messages.flash_partner_created'));
    }

    public function show(Partner $partner)
    {
        $currentYear = AcademicYear::find($partner->academic_year_id);
        $netProfit = $currentYear ? $this->netProfitSinceYearStart($currentYear) : 0;
        $entitled = $netProfit * ((float) $partner->percentage / 100);

        $withdrawals = $partner->withdrawals()->orderByDesc('withdrawal_date')->get();

        $moneySources = MoneySource::all();

        return view('admin.partners.show', compact('partner', 'netProfit', 'entitled', 'withdrawals', 'moneySources'));
    }

    public function storeWithdrawal(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'withdrawal_date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'money_source_id' => 'required|exists:money_sources,id',
        ]);

        DB::transaction(function () use ($validated, $partner) {
            $transaction = MoneyTransaction::create([
                'money_source_id' => $validated['money_source_id'],
                'direction' => 'out',
                'amount' => $validated['amount'],
                'description' => __('messages.treasury_desc_partner_withdrawal', [], 'ar').': '.$partner->name,
                'category' => 'partner_withdrawal',
                'transaction_date' => $validated['withdrawal_date'],
                'recorded_by' => auth()->id(),
            ]);

            PartnerWithdrawal::create([
                'partner_id' => $partner->id,
                'amount' => $validated['amount'],
                'withdrawal_date' => $validated['withdrawal_date'],
                'note' => $validated['note'] ?? null,
                'recorded_by' => auth()->id(),
                'money_transaction_id' => $transaction->id,
            ]);
        });

        return redirect()->route('admin.partners.show', $partner)->with('success', __('messages.flash_partner_withdrawal_created'));
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', __('messages.flash_partner_deleted'));
    }
}