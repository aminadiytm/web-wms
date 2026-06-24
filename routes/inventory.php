<?php

use App\Http\Controllers\Inventory\StockController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
        ->prefix('Inventory')
        ->name('inventory.')
        ->group(function() {
            Route::get('Stock', [StockController::class, 'index'])->name('stockIndex');
            Route::get('Stock/List', [StockController::class, 'list'])->name('stockList');
            Route::get('Stock/Available', [StockController::class, 'available'])->name('stockAvailable');
        });