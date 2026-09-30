<?php

declare(strict_types=1);

use Domain\Client\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('clients', [ClientController::class, 'index']);
Route::get('clients/{id}', [ClientController::class, 'show']);
