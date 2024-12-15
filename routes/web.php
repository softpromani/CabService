<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin',[AuthController::class, 'index'])->name('admin');
Route::post('/login',action: [AuthController::class, 'store'])->name('login');



Route::group(['name' => 'admin', 'prefix' => 'admin', 'as' => 'admin.'], function(){
Route::get('/dashboard',action: [AdminController::class, 'index'])->name(name: 'dashboard');

});
