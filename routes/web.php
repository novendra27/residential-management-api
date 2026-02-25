<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResidentController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::post('/auth/login', [AuthController::class, 'login']);

// ─── Protected (requires valid session token) ─────────────────────────────────
Route::middleware('auth.token')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Residents
    Route::get('/residents',        [ResidentController::class, 'index']);
    Route::get('/residents/{id}',   [ResidentController::class, 'show']);
    Route::post('/residents',       [ResidentController::class, 'store']);
    Route::put('/residents/{id}',   [ResidentController::class, 'update']);
    Route::delete('/residents/{id}',[ResidentController::class, 'destroy']);

    // Houses
    Route::get('/houses',                          [HouseController::class, 'index']);
    Route::get('/houses/{id}',                     [HouseController::class, 'show']);
    Route::get('/houses/{id}/resident_histories',  [HouseController::class, 'residentHistories']);
    Route::get('/houses/{id}/payment_histories',   [HouseController::class, 'paymentHistories']);
    Route::post('/houses',                         [HouseController::class, 'store']);
    Route::put('/houses/{id}',                     [HouseController::class, 'update']);
    Route::delete('/houses/{id}',                  [HouseController::class, 'destroy']);

    // Bills
    Route::get('/bills',            [BillController::class, 'index']);
    Route::get('/bills/{id}',       [BillController::class, 'show']);
    Route::post('/bills',           [BillController::class, 'store']);
    Route::put('/bills/{id}',       [BillController::class, 'update']);
    Route::patch('/bills/{id}/pay', [BillController::class, 'pay']);

    // Payments
    Route::post('/payments', [PaymentController::class, 'store']);

    // Expenses
    Route::get('/expenses',         [ExpenseController::class, 'index']);
    Route::get('/expenses/{id}',    [ExpenseController::class, 'show']);
    Route::post('/expenses',        [ExpenseController::class, 'store']);
    Route::put('/expenses/{id}',    [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

    // Report
    Route::get('/report/summary',  [ReportController::class, 'summary']);
    Route::get('/report/balances', [ReportController::class, 'balances']);
});
