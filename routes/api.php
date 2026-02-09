<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('stores', StoreController::class);
    
    Route::prefix('stores/{store}')->group(function () {
        Route::post('/cashiers', [StoreController::class, 'assignCashier']);
        Route::delete('/cashiers/{userId}', [StoreController::class, 'removeCashier']);
        
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions/income', [TransactionController::class, 'storeIncome']);
        Route::post('/transactions/expense', [TransactionController::class, 'storeExpense']);
        Route::get('/transactions/today-income', [TransactionController::class, 'todayIncome']);
        
        Route::get('/reports/daily', [ReportController::class, 'dailyReport']);
        Route::get('/reports/weekly', [ReportController::class, 'weeklyReport']);
        Route::get('/reports/monthly', [ReportController::class, 'monthlyReport']);
    });
});
