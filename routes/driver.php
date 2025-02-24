<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Driver\CarController;
use App\Http\Controllers\Api\Driver\CountryStateCityController;
use App\Http\Controllers\Api\Driver\ProfileController;
use App\Http\Controllers\Api\Driver\RideController;
use App\Http\Controllers\Api\Driver\RideScheduleController;
use Illuminate\Support\Facades\Route;

// Driver Api

Route::group(['prefix' => 'driver', 'as' => 'driver.'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/profile-update', [ProfileController::class, 'profile_update'])->name('profile-update');
        Route::post('/ride-schedule', [RideScheduleController::class, 'schedule']);

        Route::get('/countries', [CountryStateCityController::class, 'getCountries']);
        Route::get('/states/{country_id}', [CountryStateCityController::class, 'getStates']);
        Route::get('/cities/{state_id}', [CountryStateCityController::class, 'getCities']);

        Route::group(['prefix' => 'car', 'as' => 'car.'], function () {

            Route::post('/add', [CarController::class, 'car']);
            Route::post('/update/{carId}', [CarController::class, 'updateCar']);
            Route::get('/list', [CarController::class, 'carView']);
            Route::get('/brand', [CarController::class, 'brand']);
            Route::get('/model/{brand_id}', [CarController::class, 'model']);

        });
        Route::group(['prefix' => 'routes'], function () {
            Route::get('/', [RideController::class, 'getRoutes']);
            Route::get('/station/{route_id}', [RideController::class, 'getStations']);
            Route::post('/create', [RideController::class, 'store']);
            Route::post('/your-ride', [RideController::class, 'driver_rides']);
        });

    });
});
