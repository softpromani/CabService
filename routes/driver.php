<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Driver\CarController;
use App\Http\Controllers\Api\Driver\CountryStateCityController;
use App\Http\Controllers\Api\Driver\ProfileController;
use App\Http\Controllers\Api\Driver\RideController;
use App\Http\Controllers\Driver\RideScheduleController;
use Illuminate\Support\Facades\Route;

// Driver Api

Route::group(['prefix' => 'driver', 'as' => 'driver.'], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(['auth:api'])->group(function () {
        Route::put('/profile-update', [ProfileController::class, 'profile_update']);

        Route::post('/ride-schedule', [RideScheduleController::class, 'schedule']);

        Route::get('/countries', [CountryStateCityController::class, 'getCountries']);
        Route::get('/states/{country_id}', [CountryStateCityController::class, 'getStates']);
        Route::get('/cities/{state_id}', [CountryStateCityController::class, 'getCities']);

        Route::group(['prefix' => 'car', 'as' => 'car.'], function () {
            Route::post('/add', [CarController::class, 'car']);
            Route::get('/brand', [CarController::class, 'brand']);
            Route::get('/model/{brand_id}', [CarController::class, 'model']);

        });
        Route::group(['prefix' => 'routes'], function () {
            Route::get('/', [RideController::class, 'getRoutes']);
            Route::post('/add', [RideController::class, 'addRoute']);
        });

        Route::group(['prefix' => 'stations'], function () {
            Route::get('/{route_id}', [RideController::class, 'getStations']);
            Route::post('/add', [RideController::class, 'addStation']);
        });

    });
});
