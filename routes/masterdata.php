<?php

use App\Http\Controllers\MasterData\ApprovalRouteController;
use App\Http\Controllers\MasterData\CategoryController;
use App\Http\Controllers\MasterData\LocationController;
use App\Http\Controllers\MasterData\ProductController;
use App\Http\Controllers\MasterData\WarehouseController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')
    ->prefix('MasterData')
    ->name('masterdata.')
    ->group(function() {

    // --------------------------- CATEGORY --------------------------
        Route::get('Category', [CategoryController::class, 'index'])->name('catIndex');
        Route::get('Category/List', [CategoryController::class, 'list'])->name('catList');
        Route::post('Category', [CategoryController::class, 'store'])->name('catStore');
        Route::get('Category/GetData/{id}', [CategoryController::class, 'getdata'])->name('getData');
        Route::delete('Category/Delete/{id}', [CategoryController::class, 'delete'])->name('catDelete');


    // ---------------------------- PRODUCT --------------------------
        Route::get('Product', [ProductController::class, 'index'])->name('prdIndex');
        Route::get('Product/List', [ProductController::class, 'list'])->name('prdList');
        Route::post('Product', [ProductController::class, 'store'])->name('prdStore');
        Route::get('Product/GetEdit/{id}', [ProductController::class, 'getedit'])->name('getEdit');
        Route::delete('Product/Delete/{id}', [ProductController::class, 'delete'])->name('prdDelete');


    // ---------------------------- WAREHOUSE --------------------------
        Route::get('Warehouse', [WarehouseController::class, 'index'])->name('whIndex');
        Route::get('Warehouse/List', [WarehouseController::class, 'list'])->name('whList');
        Route::post('Warehouse', [WarehouseController::class, 'store'])->name('whStore');
        Route::get('Warehouse/GetData/{id}', [WarehouseController::class, 'getdata'])->name('whgetData');
        Route::delete('Warehouse/Delete/{id}', [WarehouseController::class, 'delete'])->name('whDelete');


    // ---------------------------- LOCATION --------------------------
        Route::get('Location', [LocationController::class, 'index'])->name('locIndex');
        Route::get('Location/List', [LocationController::class, 'list'])->name('locList');
        Route::post('Location', [LocationController::class, 'store'])->name('locStore');
        Route::get('Location/GetEdit/{id}', [LocationController::class, 'getdata'])->name('locgetData');
        Route::delete('Location/Delete/{id}', [LocationController::class, 'delete'])->name('locDelete');


    // ---------------------------- APPROVAL ROUTE --------------------------
        Route::get('Approval-Route', [ApprovalRouteController::class, 'index'])->name('approvalRouteIndex');
        Route::get('Approval-Route/List', [ApprovalRouteController::class, 'list'])->name('approvalRouteList');
        Route::post('Approval-Route/Store', [ApprovalRouteController::class, 'store'])->name('approvalRouteStore');
        Route::get('Approval-Route/Edit/{id}', [ApprovalRouteController::class, 'edit'])->name('approvalRouteEdit');
        Route::put('Approval-Route/Update/{id}', [ApprovalRouteController::class, 'update'])->name('approvalRouteUpdate');
        Route::delete('Approval-Route/Delete/{id}', [ApprovalRouteController::class, 'destroy'])->name('approvalRouteDelete');

    });