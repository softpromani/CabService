<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin/login', [AuthController::class, 'login'])->name('login');
Route::post('/login/store', action: [AuthController::class, 'loginStore'])->name('loginStore');
Route::get('/register',  [AuthController::class, 'register'])->name('register');
Route::get('admin/store',  [AuthController::class, 'adminStore'])->name('adminStore');



Route::group(['name' => 'admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/dashboard', action: [AdminController::class, 'index'])->name(name: 'dashboard');
});


// Route::prefix('admin')->name('admin.')->group(function () {

//     Route::middleware(['guest:auth'])->group(function () {
//         Route::get('/login', [AuthController::class, 'login'])->name('login');
//         Route::post('/login', [AuthController::class, 'loginStore'])->name('loginStore');
//         Route::get('/register', [AuthController::class, 'register'])->name('register');
//     });

//     Route::middleware(['auth:auth'])->group(function () {
//         Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
//         Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
//     });
// });
