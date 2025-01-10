<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\admin\CscController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [AuthController::class, 'login'])->name('login');
Route::post('/login/store', action: [AuthController::class, 'loginStore'])->name('loginStore');

Route::group(['name' => 'admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', action: [AdminController::class, 'index'])->name(name: 'dashboard');

    Route::get('/user-list', action: [AdminController::class, 'userList'])->name(name: 'userList');
    Route::get('/business', action: [AdminController::class, 'business_setting'])->name('business');
    Route::post('/business-setting', [AdminController::class, 'business_update'])->name('business-Setting');

    Route::get('/add-user', action: [AdminController::class, 'addUser'])->name(name: 'addUser');
    Route::post('/user/store', action: [AdminController::class, 'storeUser'])->name(name: 'storeUser');
    Route::get('/edit-user/{id?}', action: [AdminController::class, 'editUser'])->name(name: 'editUser');

    Route::put('/update-user/{id?}', action: [AdminController::class, 'updateUser'])->name(name: 'updateUser');
    Route::get('/role/create', action: [RolePermissionController::class, 'role_create'])->name(name: 'role-create');
    Route::get('/permission/{id}/edit', action: [RolePermissionController::class, 'permission_create'])->name(name: 'permission-edit');
    Route::post('/role/store', action: [RolePermissionController::class, 'role_store'])->name(name: 'role-store');
    Route::put('/permission/{id}/update', action: [RolePermissionController::class, 'permission_update'])->name(name: 'permission-update');
    Route::group(['prefix' => 'master-setup', 'as' => 'master.'], function () {
        Route::get('/countries', [CscController::class, 'country_index'])->name('country');
        Route::post('/countries', [CscController::class, 'country_store'])->name('country_store');
        Route::delete('/countries/delete/{id}', [CscController::class, 'country_destroy'])->name('country_destroy');
        Route::get('/states', [CscController::class, 'state_index'])->name('state');
        Route::post('/states', [CscController::class, 'state_store'])->name('state.store');
        Route::delete('/states/delete/{id}', [CscController::class, 'state_destroy'])->name('state_destroy');
        Route::get('/cities', [CscController::class, 'city_index'])->name('city');
        Route::post('/cities', [CscController::class, 'city_store'])->name('city.store');
        Route::delete('/cities/delete/{id}', [CscController::class, 'city_destroy'])->name('city_destroy');

    });
});
