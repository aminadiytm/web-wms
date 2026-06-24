<?php

use App\Http\Controllers\Support\SupportController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')
    ->prefix('Support')
    ->name('support.')
    ->group(function() {

    Route::get('support-wh', [SupportController::class, 'support_wh'])->name('wh');
    Route::get('support-prod', [SupportController::class, 'support_prd'])->name('prd');
    Route::get('support-loc/{id}', [SupportController::class, 'support_loc'])->name('loc');

    });