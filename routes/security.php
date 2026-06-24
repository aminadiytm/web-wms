<?php

use App\Http\Controllers\Security\RolePermissionController;
use App\Http\Controllers\Security\UserAccessController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')
    ->prefix('Security')
    ->name('security.')
    ->group(function () {

        Route::get('Role-Permission', [RolePermissionController::class, 'index'])
            ->name('roleIndex');

        Route::get('Role-Permission/List', [RolePermissionController::class, 'list'])
            ->name('roleList');

        Route::post('Role-Permission/Store', [RolePermissionController::class, 'store'])
            ->name('roleStore');

        Route::get('Role-Permission/Edit/{id}', [RolePermissionController::class, 'edit'])
            ->name('roleEdit');

        Route::put('Role-Permission/Update/{id}', [RolePermissionController::class, 'update'])
            ->name('roleUpdate');

        Route::delete('Role-Permission/Delete/{id}', [RolePermissionController::class, 'destroy'])
            ->name('roleDelete');


        Route::get('User-Access', [UserAccessController::class, 'index'])
            ->name('userAccessIndex');

        Route::get('User-Access/List', [UserAccessController::class, 'list'])
            ->name('userAccessList');

        Route::get('User-Access/Edit/{id}', [UserAccessController::class, 'edit'])
            ->name('userAccessEdit');

        Route::put('User-Access/Update/{id}', [UserAccessController::class, 'update'])
            ->name('userAccessUpdate');

    });