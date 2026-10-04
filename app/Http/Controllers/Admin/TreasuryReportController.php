<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoneySource;
use App\Models\MoneyTransaction;
use Illuminate\Http\Request;

class TreasuryReportController extends Controller
{
    public function monthly(Request $request)
    {
        [$year, $sourceId] = $this->filters($request);

        $months = $this->monthlySummary($year, $sourceId);
        $totals = $this->totals($months);
        $sources = MoneySource::all();

        return view('admin.treasury.report', compact('year', 'sourceId', 'months', 'totals', 'sources'));
    }

    public function exportMonthly(Request $request)
    {
        [$year, $sourceId] = $this->filters($request);

        $months = $this->monthlySummary($year, $sourceId);

        return response()->streamDownload(function () use ($months, $year) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                __('messages.treasury_report_col_month'),
                __('messages.treasury_report_col_income'),
                __('messages.treasury_report_col_expenses'),
                __('messages.treasury_report_col_partners'),
                __('messages.treasury_report_col_net'),
            ]);

            foreach ($months as $month => $row) {
                fputcsv($handle, [
                    sprintf('%02d/%d', $month, $year),
                    number_format($row['income'], 2, '.', ''),
                    number_format($row['expenses'], 2, '.', ''),
                    number_format($row['partners'], 2, '.', ''),
                    number_format($row['net'], 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, "treasury-report-{$year}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filters(Request $request): array
    {
        $year = (int) $request->query('year', now()->year);
        $sourceId = $request->filled('money_source_id') ? (int) $request->query('money_source_id') : null;

        return [$year, $sourceId];
    }

    private function monthlySummary(int $year, ?int $sourceId): array
    {
        $query = MoneyTransaction::query()->whereYear('transaction_date', $year);

        if ($sourceId) {
            $query->where('money_source_id', $sourceId);
        }

        $rows = $query->selectRaw('MONTH(transaction_date) as month, direction, category, SUM(amount) as total')
            ->groupBy('month', 'direction', 'category')
            ->get();

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = ['income' => 0.0, 'expenses' => 0.0, 'partners' => 0.0];
        }

        foreach ($rows as $row) {
            $month = (int) $row->month;
            $total = (float) $row->total;

            if ($row->direction === 'in') {
                $months[$month]['income'] += $total;
            } elseif ($row->category === 'partner_withdrawal') {
                $months[$month]['partners'] += $total;
            } else {
                $months[$month]['expenses'] += $total;
            }
        }

        foreach ($months as $month => $row) {
            $months[$month]['net'] = $row['income'] - $row['expenses'];
        }

        return $months;
    }

    private function totals(array $months): array
    {
        $totals = ['income' => 0.0, 'expenses' => 0.0, 'partners' => 0.0, 'net' => 0.0];

        foreach ($months as $row) {
            foreach ($totals as $key => $value) {
                $totals[$key] += $row[$key];
            }
        }

        return $totals;
    }
}
