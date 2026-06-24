<?php

use App\Http\Controllers\ApprovalController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('Approval')
    ->name('approval.')
    ->group(function() {
        Route::get('Approval/{token}/View', [ApprovalController::class, 'view'])
            ->name('view');

        Route::get('Approval/{token}/Approve', [ApprovalController::class, 'approve'])
            ->name('approve');

        Route::get('Approval/{token}/Reject', [ApprovalController::class, 'reject'])
            ->name('reject');
    });
