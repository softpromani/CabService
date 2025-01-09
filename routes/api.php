<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/profile-update', [ProfileController::class, 'profile_update'])->name('profile-update');
Route::post('/car', [CarController::class, 'car'])->name('car');
Route::post('/route', [CarController::class, 'route'])->name('route');
Route::post('login/driver', [AuthController::class, 'driverLogin']);

Route::middleware('api')->group(function () {
    Route::post('user/login', [AuthController::class, 'userLogin']);
});

require base_path('routes/user_api_routes.php');
