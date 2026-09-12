<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoneySource;
use App\Models\MoneyTransaction;
use Illuminate\Http\Request;

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

        return view('admin.treasury.index', compact('sourcesData', 'totalBalance'));
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

        return view('admin.treasury.transactions', compact('source', 'transactions'));
    }

    public function storeTransaction(Request $request, MoneySource $source)
    {
        $validated = $request->validate([
            'direction' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
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
            'description' => $validated['description'],
            'transaction_date' => $validated['transaction_date'],
            'document_path' => $documentPath,
            'recorded_by' => auth()->id(),
        ]);

        return redirect()->route('admin.treasury.transactions', $source)
            ->with('success', __('messages.flash_treasury_transaction_created'));
    }
}