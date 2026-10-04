<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoneySource;
use App\Models\MoneyTransaction;
use App\Models\MoneyTransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TreasuryController extends Controller
{
    // الصفحة الرئيسية: كل مصدر ورصيده + المجموع الكلي
    public function index()
    {
        $sources = MoneySource::all();

        $sourcesData = $sources->map(function ($source) {
            return [
                'source' => $source,
                'balance' => $source->balance(),
            ];
        });

        $totalBalance = $sourcesData->sum('balance');

        $categoryTotals = MoneyTransaction::query()
            ->selectRaw('direction, category, COUNT(*) as entries_count, SUM(amount) as total')
            ->groupBy('direction', 'category')
            ->orderBy('direction')
            ->orderBy('category')
            ->get();

        return view('admin.treasury.index', compact('sourcesData', 'totalBalance', 'categoryTotals'));
    }

    // إدارة مصادر الأموال (إضافة بنك جديد، تطبيق جديد...)
    public function sources()
    {
        $sources = MoneySource::all();

        return view('admin.treasury.sources', compact('sources'));
    }

    public function storeSource(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,mobile_app,cash',
        ]);

        MoneySource::create($validated);

        return redirect()->route('admin.treasury.sources')
            ->with('success', __('messages.flash_treasury_source_created'));
    }

    public function destroySource(MoneySource $source)
    {
        $source->delete();

        return redirect()->route('admin.treasury.sources')
            ->with('success', __('messages.flash_treasury_source_deleted'));
    }

    // عرض حركات مصدر معين
    public function transactions(MoneySource $source)
    {
        $transactions = $source->transactions()->with('recordedBy')->orderBy('transaction_date', 'desc')->get();

        $logs = MoneyTransactionLog::with('user')
            ->whereHas('transaction', fn ($q) => $q->withTrashed()->where('money_source_id', $source->id))
            ->latest('id')
            ->limit(50)
            ->get();

        return view('admin.treasury.transactions', compact('source', 'transactions', 'logs'));
    }

    public function editTransaction(MoneySource $source, MoneyTransaction $transaction)
    {
        $this->ensureEditable($source, $transaction);

        return view('admin.treasury.edit-transaction', compact('source', 'transaction'));
    }

    public function updateTransaction(Request $request, MoneySource $source, MoneyTransaction $transaction)
    {
        $this->ensureEditable($source, $transaction);

        $validated = $request->validate([
            'direction' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'category' => ['required', Rule::in(MoneyTransaction::categoriesFor($request->input('direction')))],
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'document' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')->store('treasury_documents', 'public');
        }

        DB::transaction(function () use ($validated, $transaction) {
            $before = $this->snapshot($transaction);

            $transaction->update($validated);

            $changes = [];
            foreach ($this->snapshot($transaction->fresh()) as $field => $value) {
                if ($before[$field] !== $value) {
                    $changes[$field] = ['old' => $before[$field], 'new' => $value];
                }
            }

            if ($changes) {
                $transaction->logs()->create([
                    'user_id' => auth()->id(),
                    'action' => 'updated',
                    'description' => $transaction->description,
                    'details' => $changes,
                ]);
            }
        });

        return redirect()->route('admin.treasury.transactions', $source)
            ->with('success', __('messages.treasury_flash_transaction_updated'));
    }

    public function destroyTransaction(MoneySource $source, MoneyTransaction $transaction)
    {
        $this->ensureEditable($source, $transaction);

        DB::transaction(function () use ($transaction) {
            $transaction->logs()->create([
                'user_id' => auth()->id(),
                'action' => 'cancelled',
                'description' => $transaction->description,
                'details' => $this->snapshot($transaction),
            ]);

            $transaction->delete();
        });

        return redirect()->route('admin.treasury.transactions', $source)
            ->with('success', __('messages.treasury_flash_transaction_cancelled'));
    }

    private function ensureEditable(MoneySource $source, MoneyTransaction $transaction): void
    {
        abort_unless(
            $transaction->money_source_id === $source->id && $transaction->isManual(),
            403,
            __('messages.treasury_system_entry_error')
        );
    }

    private function snapshot(MoneyTransaction $transaction): array
    {
        return [
            'direction' => $transaction->direction,
            'amount' => number_format((float) $transaction->amount, 2, '.', ''),
            'category' => $transaction->category,
            'description' => $transaction->description,
            'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
        ];
    }

    public function storeTransaction(Request $request, MoneySource $source)
    {
        $validated = $request->validate([
            'direction' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'category' => ['required', Rule::in(MoneyTransaction::categoriesFor($request->input('direction')))],
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'document' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('treasury_documents', 'public');
        }

        MoneyTransaction::create([
            'money_source_id' => $source->id,
            'direction' => $validated['direction'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'transaction_date' => $validated['transaction_date'],
            'document_path' => $documentPath,
            'recorded_by' => auth()->id(),
        ]);

        return redirect()->route('admin.treasury.transactions', $source)
            ->with('success', __('messages.flash_treasury_transaction_created'));
    }
}