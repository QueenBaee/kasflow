<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => Inertia::render('Auth/Login'))->name('login');
    Route::get('/register', fn() => Inertia::render('Auth/Register'))->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    // Owner-only routes
    Route::middleware('owner')->group(function () {
        Route::get('/dashboard', function (Request $request) {
            $user = auth()->user();
            $stores = $user->stores;
            $storeId = $request->input('store_id');
            $currentStore = $storeId ? $stores->firstWhere('id', $storeId) : $stores->first();
            
            $summary = ['todayIncome' => 0, 'todayExpense' => 0, 'todayProfit' => 0];
            $recentTransactions = [];
            
            if ($currentStore) {
                $today = now()->format('Y-m-d');
                $todayIncome = $currentStore->transactions()->where('type', 'income')->where('transaction_date', $today)->sum('amount');
                $todayExpense = $currentStore->transactions()->where('type', 'expense')->where('transaction_date', $today)->sum('amount');
                $summary = [
                    'todayIncome' => (float) $todayIncome,
                    'todayExpense' => (float) $todayExpense,
                    'todayProfit' => (float) ($todayIncome - $todayExpense),
                ];
                $recentTransactions = $currentStore->transactions()->with('user')->latest('transaction_date')->latest('created_at')->limit(10)->get();
            }
            
            return Inertia::render('Owner/Dashboard', compact('stores', 'currentStore', 'summary', 'recentTransactions'));
        })->name('dashboard');

        Route::get('/expenses', fn() => Inertia::render('Owner/Expenses', [
            'stores' => auth()->user()->stores,
            'currentStore' => auth()->user()->stores()->first(),
        ]))->name('expenses.index');

        Route::get('/reports', function () {
            $user = auth()->user();
            $currentStore = $user->stores()->first();
            $dailyReport = ['total_income' => 0, 'total_expense' => 0, 'profit' => 0];
            
            if ($currentStore) {
                $today = now()->format('Y-m-d');
                $todayIncome = $currentStore->transactions()->where('type', 'income')->where('transaction_date', $today)->sum('amount');
                $todayExpense = $currentStore->transactions()->where('type', 'expense')->where('transaction_date', $today)->sum('amount');
                $dailyReport = [
                    'total_income' => (float) $todayIncome,
                    'total_expense' => (float) $todayExpense,
                    'profit' => (float) ($todayIncome - $todayExpense),
                ];
            }
            
            return Inertia::render('Owner/Reports', [
                'stores' => $user->stores,
                'currentStore' => $currentStore,
                'dailyReport' => $dailyReport,
            ]);
        })->name('reports.index');

        Route::get('/cashiers', function () {
            $user = auth()->user();
            $currentStore = $user->stores()->first();
            $cashiers = $currentStore ? $currentStore->cashiers : [];
            
            return Inertia::render('Owner/Cashiers', [
                'stores' => $user->stores,
                'currentStore' => $currentStore,
                'cashiers' => $cashiers,
            ]);
        })->name('cashiers.index');

        Route::get('/stores', fn() => Inertia::render('Owner/Stores', [
            'stores' => auth()->user()->stores,
            'currentStore' => auth()->user()->stores()->first(),
        ]))->name('stores.index');

        Route::post('/stores/{store}/cashiers/assign', [\App\Http\Controllers\StoreController::class, 'assignCashier'])->name('cashiers.assign');
        Route::delete('/stores/{store}/cashiers/{user}', [\App\Http\Controllers\StoreController::class, 'removeCashier'])->name('cashiers.remove');
        Route::post('/stores/{store}/transactions/expense', [TransactionController::class, 'storeExpense'])->name('transactions.expense.store');
    });

    // Shared route (Owner + Cashier)
    Route::post('/stores/{store}/transactions/income', [TransactionController::class, 'storeIncome'])->name('transactions.income.store');

    // Cashier routes
    Route::prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/', function () {
            $user = auth()->user();
            $store = $user->stores()->first();
            
            if (!$store) {
                return redirect()->route('dashboard')->with('error', 'No store assigned');
            }
            
            $today = now()->format('Y-m-d');
            $todayIncome = $store->transactions()->where('type', 'income')->where('transaction_date', $today)->sum('amount');
            $transactionCount = $store->transactions()->where('type', 'income')->where('transaction_date', $today)->count();
            $recentTransactions = $store->transactions()->where('type', 'income')->where('transaction_date', $today)->latest('created_at')->limit(10)->get();
            
            return Inertia::render('Cashier/Index', [
                'store' => $store,
                'todayIncome' => (float) $todayIncome,
                'transactionCount' => $transactionCount,
                'recentTransactions' => $recentTransactions,
            ]);
        })->name('home');

        Route::get('/income', function () {
            $user = auth()->user();
            $store = $user->stores()->first();
            
            if (!$store) {
                return redirect()->route('cashier.home')->with('error', 'No store assigned');
            }
            
            return Inertia::render('Cashier/Income', ['store' => $store]);
        })->name('income.create');
    });
});
