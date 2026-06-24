<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\MasterData\CategoryController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/summary', [DashboardController::class, 'summary'])
        ->name('dashboard.summary');

	// Route::get('MasterData/Category', [CategoryController::class, 'index']);

	Route::get('user-management', function () {
		return view('laravel-examples/user-management');
	})->name('user-management');

	Route::get('tables', function () {
		return view('tables');
	})->name('tables');

    Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

    Route::get('static-sign-up', function () {
		return view('static-sign-up');
	})->name('sign-up');

    Route::post('/logout', [SessionsController::class, 'destroy']);
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/login', function () {
		$pageTitle = "Dashboard";

		return view('dashboard', compact('pageTitle'));
	})->name('sign-up');

	// ------------------------------- USER LIST -----------------------------------------
	Route::middleware(['auth', 'check.menu.permission'])
	    ->prefix('admin/users')
	    ->name('admin.users.')
	    ->group(function () {

	        Route::get('/', [UserManagementController::class, 'index'])
	            ->name('usrIndex');

	        Route::get('/list', [UserManagementController::class, 'userList'])
	            ->name('usrList');

	        Route::post('/store', [UserManagementController::class, 'store'])
	            ->name('usrStore');

	        Route::get('/edit/{id}', [UserManagementController::class, 'getEdit'])
	            ->name('usrEdit');

	        Route::post('/reset-password', [UserManagementController::class, 'resetPassword'])
	            ->name('usrUpdate');

	        Route::delete('/delete/{id}', [UserManagementController::class, 'delete'])
	            ->name('usrDelete');
	    });

});



Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');