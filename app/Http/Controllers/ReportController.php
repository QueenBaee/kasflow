<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    public function dailyReport(Request $request, Store $store): JsonResponse
    {
        $this->authorize('viewReports', $store);

        $request->validate([
            'date' => ['required', 'date'],
        ]);

        $report = $this->reportService->dailyReport($store->id, $request->date);

        return response()->json(['data' => $report]);
    }

    public function weeklyReport(Request $request, Store $store): JsonResponse
    {
        $this->authorize('viewReports', $store);

        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $report = $this->reportService->weeklyReport(
            $store->id,
            $request->start_date,
            $request->end_date
        );

        return response()->json(['data' => $report]);
    }

    public function monthlyReport(Request $request, Store $store): JsonResponse
    {
        $this->authorize('viewReports', $store);

        $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000'],
        ]);

        $report = $this->reportService->monthlyReport(
            $store->id,
            $request->month,
            $request->year
        );

        return response()->json(['data' => $report]);
    }
}
