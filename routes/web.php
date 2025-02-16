<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\admin\CscController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ModelController;
use App\Http\Controllers\admin\RouteController;
use App\Http\Controllers\admin\DriverController;
use App\Http\Controllers\admin\TicketController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Admin\FareSetupController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\admin\UserProfileController;
use App\Http\Controllers\Admin\BusinessPageController;
use App\Http\Controllers\Admin\BusinessSettingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [AuthController::class, 'login'])->name('login');
Route::post('/login/store', action: [AuthController::class, 'loginStore'])->name('loginStore');

Route::group(['name' => 'admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', action: [AdminController::class, 'index'])->name(name: 'dashboard');

    Route::get('/user-list', action: [AdminController::class, 'userList'])->name(name: 'userList');
    Route::put('/update-user/{id?}', action: [AdminController::class, 'updateUser'])->name(name: 'updateUser');
    Route::get('/add-user', action: [UserProfileController::class, 'addUser'])->name(name: 'addUser');
    Route::get('/edit-user/{id?}', action: [AdminController::class, 'editUser'])->name('editUser');
    Route::get('/edit-user-profile/{id?}', action: [UserProfileController::class, 'userProfile'])->name(name: 'userProfile');
    Route::put('/update-user-profile/{id?}', action: [UserProfileController::class, 'updateUserProfile'])->name(name: 'updateUserProfile');
    Route::post('driver/change-password/{id?}', [DriverController::class, 'driverChangePassword'])->name('driverChangePassword');

    Route::post('/change-password/{id?}', [UserProfileController::class, 'changePassword'])->name('changePassword');

    Route::get('/get-states/{countryId}', [AdminController::class, 'getStates'])->name('getStates');
    Route::get('/get-cities/{stateId}', [AdminController::class, 'getCities'])->name('getCities');

    Route::get('/business', action: [AdminController::class, 'business_setting'])->name('business');
    Route::post('/business-setting', [AdminController::class, 'business_update'])->name('business-Setting');

    Route::get('/add-user', action: [AdminController::class, 'addUser'])->name(name: 'addUser');
    Route::post('/user/store', action: [AdminController::class, 'storeUser'])->name(name: 'storeUser');

    Route::resource('/driver', DriverController::class);
    Route::get('driver/profile/{id}', [DriverController::class, 'profile'])->name('driver.profile');
    Route::get('driver/cars/{id}', [DriverController::class, 'cars'])->name('driver.cars');
    Route::get('driver/cars/{id}/view', [DriverController::class, 'viewCars'])->name('cars.viewCars');
    Route::get('driver/cars/{id}/edit', [DriverController::class, 'editcar'])->name('cars.editcar');
    Route::post('driver/cars/{id}/update', [DriverController::class, 'updateCar'])->name('cars.updateCar');
    Route::put('driver/update/{id?}', [DriverController::class, 'updateDriver'])->name('updateDriver');

    Route::get('/get-models/{brandId}', [DriverController::class, 'getModels'])->name('getModels');
    Route::post('driver/car/{id}/delete-image/{image}', [DriverController::class, 'deleteImage'])->name('car.deleteImage');

    Route::resource('/customer', UserController::class);
    Route::post('/user-suspend-status', [UserController::class, 'user_suspend_status'])->name('user.status.update'); // for all user

    Route::get('/role/create', action: [RolePermissionController::class, 'role_create'])->name(name: 'role-create');
    Route::get('/permission/{id}/edit', action: [RolePermissionController::class, 'permission_create'])->name(name: 'permission-edit');
    Route::post('/role/store', action: [RolePermissionController::class, 'role_store'])->name(name: 'role-store');
    Route::put('/permission/{id}/update', action: [RolePermissionController::class, 'permission_update'])->name(name: 'permission-update');

    Route::group(['prefix' => 'master-setup', 'as' => 'master.'], function () {
        Route::get('/countries', [CscController::class, 'country_index'])->name('country');
        Route::post('/countries', [CscController::class, 'country_store'])->name('country_store');
        Route::delete('/countries/delete/{id}', [CscController::class, 'country_destroy'])->name('country_destroy');
        Route::get('/countries/{id}/edit', [CscController::class, 'editCountry'])->name('editCountry');
        Route::put('/countries/update/{id}', [CscController::class, 'updateCountry'])->name('updateCountry');
        Route::get('/states', [CscController::class, 'state_index'])->name('state');
        Route::post('/states', [CscController::class, 'state_store'])->name('state.store');
        Route::get('/states/{id}/edit', [CscController::class, 'editState'])->name('editState');
        Route::put('/states/update/{id}', [CscController::class, 'updateState'])->name('updateState');
        Route::delete('/states/delete/{id}', [CscController::class, 'state_destroy'])->name('state_destroy');
        Route::get('/cities', [CscController::class, 'city_index'])->name('city');
        Route::post('/cities', [CscController::class, 'city_store'])->name('city.store');
        Route::get('/cities/{id}/edit', [CscController::class, 'editCity'])->name('editCity');
        Route::put('/cities/update/{id}', [CscController::class, 'updateCity'])->name('updateCity');
        Route::delete('/cities/delete/{id}', [CscController::class, 'city_destroy'])->name('city_destroy');
        Route::get('/brands', [BrandController::class, 'brand_index'])->name('brand');
        Route::post('/brands', [BrandController::class, 'brand_store'])->name('brand_store');
        Route::delete('/brands/delete/{id}', [BrandController::class, 'brand_destroy'])->name('brand_destroy');
        Route::get('/brands/{id}/edit', [BrandController::class, 'editBrand'])->name('editBrand');
        Route::put('/brands/update/{id}', [BrandController::class, 'updateBrand'])->name('updateBrand');
        Route::get('/models', [ModelController::class, 'model_index'])->name('model');
        Route::post('/models', [ModelController::class, 'model_store'])->name('model_store');
        Route::delete('/models/delete/{id}', [ModelController::class, 'model_destroy'])->name('model_destroy');
        Route::get('/models/{id}/edit', [ModelController::class, 'editModel'])->name('editModel');
        Route::put('/models/update/{id}', [ModelController::class, 'updateModel'])->name('updateModel');
        Route::resource('fare-setup', FareSetupController::class);
        Route::resource('route-setup', RouteController::class);
        Route::get('route-setup/stations/{id}', [RouteController::class, 'stations'])->name('route-setup.stations');
        Route::post('route-setup/stations/store/{id}', [RouteController::class, 'station_store'])->name('route-setup.station-store');
        Route::put('update-route-setup/stations/{id}', [RouteController::class, 'stationUpdate'])->name('route-setup.stationUpdate');
        Route::delete('delete-route-setup/stations/{id}', [RouteController::class, 'station_destroy'])->name('route-setup.station_destroy');
        Route::post('/route-setup-status', [RouteController::class, 'route_status'])->name('route-setup.status');

    });
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {

        Route::resource('business-setting', BusinessSettingController::class);
        Route::resource('business-pages', BusinessPageController::class);
        Route::get('third-party-api/{slug?}', [SettingController::class, 'thirdPartyApi'])->name('thirdPartyApi');
        Route::post('third-party-api-post', [SettingController::class, 'thirdPartyApiPost'])->name('thirdPartyApiPost');

        Route::resource('socialmedia', controller: SocialMediaController::class);
        Route::post('socialmedia/update-status', [SocialMediaController::class, 'updateStatus'])->name('socialmedia.update_status');

    });

    Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.'], function () {
        Route::controller(TicketController::class)->group(function () {
            Route::get('list', 'index')->name('view');
            Route::post('status', 'updateStatus')->name('status');
            Route::get('single-ticket/{id}', 'getView')->name('singleTicket');
            Route::post('reply/{id}', 'reply')->name('reply');

        });
    });

});
