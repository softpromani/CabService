<?php
use App\Http\Controllers\Api\Driver\CarController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Driver\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Driver Api
Route::post('/profile-update',[ProfileController::class,'profile_update'])->name('profile-update');
Route::post('/car',[CarController::class,'car'])->name('car');
Route::post('/route',[CarController::class,'route'])->name('route');
Route::post('/detail',[CarController::class,'detail'])->name('detail');
Route::post('login/driver', [AuthController::class, 'driverLogin']);

Route::post('login', [AuthController::class, 'login']);
Route::post('login/user', [AuthController::class, 'userLogin']);
Route::middleware('api')->group(function () {

});
