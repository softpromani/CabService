<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Driver\CarController;
use App\Http\Controllers\Api\Driver\ProfileController;
use Illuminate\Support\Facades\Route;

// Driver Api

Route::group(['prefix' => 'driver', 'as' => 'driver.'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('')->group(function () {
        Route::post('/profile-update', [ProfileController::class, 'profile_update'])->name('profile-update');
        Route::post('/car', [CarController::class, 'car'])->name('car');
        Route::post('/route', [CarController::class, 'route'])->name('route');
    });
});

require base_path('routes/user_api_routes.php');
