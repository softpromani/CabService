<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('login/driver', [AuthController::class, 'driverLogin']);
Route::post('login/user', [AuthController::class, 'userLogin']);
Route::middleware('api')->group(function () {

});
