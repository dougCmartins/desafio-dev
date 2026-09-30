<?php

declare(strict_types=1);

use Domain\Transaction\Controllers\OperationController;
use Domain\Transaction\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('transactions', [TransactionController::class, 'index']);
Route::post('transactions/import', [TransactionController::class, 'import']);
Route::delete('transactions', [TransactionController::class, 'clear']);
Route::post('transactions', [TransactionController::class, 'store']);
Route::get('transactions/{id}', [TransactionController::class, 'show']);

Route::get('operations', [OperationController::class, 'index']);
Route::get('operations/{id}', [OperationController::class, 'show']);
