<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\StoreIncomeRequest;
use App\Models\Store;
use App\Services\TransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TransactionService $transactionService)
    {
    }

    public function storeIncome(StoreIncomeRequest $request, Store $store): RedirectResponse
    {
        if (!$request->user()->hasStoreAccess($store->id)) {
            return redirect()->route('cashier.home')
                ->with('error', 'You do not have access to this store.');
        }

        $transaction = $this->transactionService->createIncome(
            $store->id,
            $request->user(),
            $request->validated()
        );

        return redirect()->route('cashier.home')
            ->with('success', 'Income recorded successfully');
    }

    public function storeExpense(StoreExpenseRequest $request, Store $store): RedirectResponse
    {
        $this->authorize('createExpense', $store);

        $transaction = $this->transactionService->createExpense(
            $store->id,
            $request->user(),
            $request->validated()
        );

        return redirect()->route('dashboard')
            ->with('success', 'Expense recorded successfully');
    }

    public function index(Request $request, Store $store): JsonResponse
    {
        $this->authorize('view', $store);

        $query = $store->transactions()->with('user');

        if ($request->user()->isStoreCashier($store->id)) {
            $query->where('type', 'income');
        }

        if ($request->has('date')) {
            $query->where('transaction_date', $request->date);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest('transaction_date')
            ->latest('created_at')
            ->paginate(50);

        return response()->json($transactions);
    }

    public function todayIncome(Store $store): JsonResponse
    {
        if (!auth()->user()->hasStoreAccess($store->id)) {
            abort(403, 'You do not have access to this store.');
        }

        $total = $this->transactionService->getTodayIncome($store->id);

        return response()->json([
            'date' => now()->format('Y-m-d'),
            'total_income' => $total,
        ]);
    }
}
