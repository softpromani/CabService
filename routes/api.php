<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Driver\CarController;
use App\Http\Controllers\Api\Driver\ProfileController;
use App\Http\Controllers\Driver\RideScheduleController;
use Illuminate\Support\Facades\Route;

// Driver Api

Route::group(['prefix' => 'driver', 'as' => 'driver.'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware(['auth:api'])->group(function () {
        Route::put('/profile-update', [ProfileController::class, 'profile_update']);
        Route::group(['prefix' => 'car', 'as' => 'car.'], function () {
            Route::post('/add', [CarController::class, 'car'])->name('add');
            Route::get('/brand', [CarController::class, 'brand'])->name('brand');
            Route::get('/model/{brand_id}', [CarController::class, 'model'])->name('model');

        });
        Route::post('/ride-schedule', [RideScheduleController::class, 'schedule']);
    });
});

require base_path('routes/user_api_routes.php');
