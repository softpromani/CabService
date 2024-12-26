<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [AuthController::class, 'login'])->name('login');
Route::post('/login/store', action: [AuthController::class, 'loginStore'])->name('loginStore');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('admin/store', [AuthController::class, 'adminStore'])->name('adminStore');

Route::group(['name' => 'admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', action: [AdminController::class, 'index'])->name(name: 'dashboard');

    Route::get('/user-list', action: [AdminController::class, 'userList'])->name(name: 'userList');
    Route::get('/add-user', action: [AdminController::class, 'addUser'])->name(name: 'addUser');
    Route::post('/user/store', action: [AdminController::class, 'storeUser'])->name(name: 'storeUser');
    Route::get('/edit-user/{id?}', action: [AdminController::class, 'editUser'])->name(name: 'editUser');

    Route::put('/update-user/{id?}', action: [AdminController::class, 'updateUser'])->name(name: 'updateUser');
    Route::get('/role/create', action: [RolePermissionController::class, 'role_create'])->name(name: 'role-create');
    Route::get('/permission/{id}/edit', action: [RolePermissionController::class, 'permission_create'])->name(name: 'permission-edit');
    Route::post('/role/store', action: [RolePermissionController::class, 'role_store'])->name(name: 'role-store');
    Route::put('/permission/{id}/update', action: [RolePermissionController::class, 'permission_update'])->name(name: 'permission-update');
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
