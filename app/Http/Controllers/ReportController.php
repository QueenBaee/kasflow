<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function exportPdf(Request $request, Store $store)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        $income = $store->transactions()
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
            
        $expense = $store->transactions()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
            
        $reportData = [
            'total_income' => (float) $income,
            'total_expense' => (float) $expense,
            'profit' => (float) ($income - $expense),
        ];
        
        $transactions = $store->transactions()
            ->with(['user', 'customer'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'store' => $store,
            'reportData' => $reportData,
            'transactions' => $transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download('report-'.$startDate.'-to-'.$endDate.'.pdf');
    }

    public function exportCsv(Request $request, Store $store)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        $transactions = $store->transactions()
            ->with(['user', 'customer'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'report-'.$startDate.'-to-'.$endDate.'.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Type', 'Customer', 'Category', 'Note', 'Amount']);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date,
                    $transaction->type,
                    $transaction->customer?->name ?? '-',
                    $transaction->category ?? '-',
                    $transaction->note ?? '-',
                    $transaction->amount,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
