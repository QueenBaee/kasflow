<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function createIncome(int $storeId, User $user, array $data): Transaction
    {
        return Transaction::create([
            'store_id' => $storeId,
            'user_id' => $user->id,
            'customer_id' => $data['customer_id'] ?? null,
            'type' => 'income',
            'amount' => $data['amount'],
            'category' => 'Payment',
            'note' => $data['note'] ?? null,
            'transaction_date' => now()->format('Y-m-d'),
        ]);
    }

    public function createExpense(int $storeId, User $user, array $data): Transaction
    {
        return Transaction::create([
            'store_id' => $storeId,
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => $data['amount'],
            'category' => $data['category'] ?? null,
            'note' => $data['note'] ?? null,
            'transaction_date' => isset($data['date']) 
                ? Carbon::parse($data['date'])->format('Y-m-d')
                : now()->format('Y-m-d'),
        ]);
    }

    public function getTodayIncome(int $storeId): float
    {
        return (float) Transaction::where('store_id', $storeId)
            ->where('type', 'income')
            ->where('transaction_date', now()->format('Y-m-d'))
            ->sum('amount');
    }
}
