<?php

namespace App\Services;

use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function dailyReport(int $storeId, string $date): array
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        
        $summary = DB::table('transactions')
            ->where('store_id', $storeId)
            ->where('transaction_date', $date)
            ->select(
                DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income'),
                DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense'),
                DB::raw('COUNT(CASE WHEN type = "income" THEN 1 END) as income_count'),
                DB::raw('COUNT(CASE WHEN type = "expense" THEN 1 END) as expense_count')
            )
            ->first();

        $totalIncome = (float) ($summary->total_income ?? 0);
        $totalExpense = (float) ($summary->total_expense ?? 0);

        return [
            'date' => $date,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'profit' => $totalIncome - $totalExpense,
            'income_count' => $summary->income_count ?? 0,
            'expense_count' => $summary->expense_count ?? 0,
        ];
    }

    public function weeklyReport(int $storeId, string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->format('Y-m-d');
        $end = Carbon::parse($endDate)->format('Y-m-d');
        
        $summary = DB::table('transactions')
            ->where('store_id', $storeId)
            ->whereBetween('transaction_date', [$start, $end])
            ->select(
                DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income'),
                DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense'),
                DB::raw('COUNT(CASE WHEN type = "income" THEN 1 END) as income_count'),
                DB::raw('COUNT(CASE WHEN type = "expense" THEN 1 END) as expense_count')
            )
            ->first();

        $totalIncome = (float) ($summary->total_income ?? 0);
        $totalExpense = (float) ($summary->total_expense ?? 0);

        return [
            'start_date' => $start,
            'end_date' => $end,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'profit' => $totalIncome - $totalExpense,
            'income_count' => $summary->income_count ?? 0,
            'expense_count' => $summary->expense_count ?? 0,
        ];
    }

    public function monthlyReport(int $storeId, int $month, int $year): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');
        
        $summary = DB::table('transactions')
            ->where('store_id', $storeId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income'),
                DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense'),
                DB::raw('COUNT(CASE WHEN type = "income" THEN 1 END) as income_count'),
                DB::raw('COUNT(CASE WHEN type = "expense" THEN 1 END) as expense_count')
            )
            ->first();

        $totalIncome = (float) ($summary->total_income ?? 0);
        $totalExpense = (float) ($summary->total_expense ?? 0);

        return [
            'month' => $month,
            'year' => $year,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'profit' => $totalIncome - $totalExpense,
            'income_count' => $summary->income_count ?? 0,
            'expense_count' => $summary->expense_count ?? 0,
        ];
    }
}
