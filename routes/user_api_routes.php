<?php

use App\Http\Controllers\Api\Users\ProfileController;
use App\Http\Controllers\Api\User\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->as('user.')->group(function () {
    Route::post('login', [AuthController::class, 'userLogin'])->name('login');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('profile-update', [ProfileController::class, 'updateProfile'])->name('profile-update');
    });
});
