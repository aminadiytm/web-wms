<?php

use App\Http\Controllers\Transaction\InboundController;
use App\Http\Controllers\Transaction\OutboundController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check.menu.permission'])
    ->prefix('Transaction')
    ->name('transaction.')
    ->group(function() {

    // --------------------------- INBOUND ------------------------------
    Route::get('Inbound', [InboundController::class, 'index'])->name('inbIndex');
    Route::get('Inbound/List', [InboundController::class, 'list'])->name('inbList');
    Route::get('Inbound/Detail-List', [InboundController::class, 'detList'])->name('inbdetList');
    Route::get('Inbound/Create', [InboundController::class, 'create'])->name('inbCreate');
    Route::post('Inbound/Store', [InboundController::class, 'store'])->name('inbStore');
    Route::get('Inbound/{id}/data',[InboundController::class, 'getEditData'])->name('inbData');
    Route::get('Inbound/Edit/{id}', [InboundController::class, 'edit'])->name('inbEdit');
    Route::put('Inbound/Update/{id}', [InboundController::class, 'update'])->name('inbUpdate');
    Route::get('Inbound/Receive/{id}', [InboundController::class, 'receive'])->name('inbReceive');
    Route::put('Inbound/Receive/{id}', [InboundController::class, 'receiveUpdate'])->name('inbReceiveUpdate');
    
    Route::get('Outbound', [OutboundController::class, 'index'])->name('outbIndex');
    Route::get('Outbound/List', [OutboundController::class, 'list'])->name('outbList');
    Route::get('Outbound/Detail-List', [OutboundController::class, 'detList'])->name('outbdetList');
    Route::get('Outbound/Create', [OutboundController::class, 'create'])->name('outbCreate');
    Route::post('Outbound/Store', [OutboundController::class, 'store'])->name('outbStore');
    Route::get('Outbound/{id}/data', [OutboundController::class, 'getEditData'])->name('outbData');
    Route::get('Outbound/Edit/{id}', [OutboundController::class, 'edit'])->name('outbEdit');
    Route::put('Outbound/Update/{id}', [OutboundController::class, 'update'])->name('outbUpdate');
    Route::get('Outbound/Confirm/{id}', [OutboundController::class, 'confirm'])->name('outbConfirm');
    Route::put('Outbound/Confirm/{id}', [OutboundController::class, 'confirmUpdate'])->name('outbConfirmUpdate');

    });