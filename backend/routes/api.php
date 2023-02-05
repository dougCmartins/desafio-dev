<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Operation\OperationController;
use App\Http\Controllers\Client\ClientController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('transactions', TransactionController::class)->except(['create', 'edit', 'destroy']);
Route::resource('operations', OperationController::class)->except(['create', 'edit', 'destroy', 'store']);
Route::resource('clients', ClientController::class)->except(['create', 'edit', 'destroy']);