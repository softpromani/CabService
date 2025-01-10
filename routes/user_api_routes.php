<?php

use App\Http\Controllers\Api\User\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::post('login', [AuthController::class, 'userLogin']);
    Route::middleware(['auth:api'])->group(function () {
        Route::post('profile', [AuthController::class, 'updateProfile']);

    });
});
