<?php

use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\BookingController;
use App\Http\Controllers\Api\User\CustomerTicketController;
use App\Http\Controllers\Api\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->as('user.')->group(function () {
    Route::post('login', [AuthController::class, 'userLogin'])->name('login');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
        Route::post('update-profile', [ProfileController::class, 'updateProfile'])->name('userProfileupdate');

        Route::group(['prefix' => 'support-ticket'], function () {
            Route::controller(CustomerTicketController::class)->group(function () {
                Route::post('create', 'SupportTicket');
                Route::get('get', 'get_support_tickets');
                Route::get('conv/{ticket_id}', 'get_support_ticket_conv');
                Route::post('reply/{ticket_id}', 'reply_support_ticket');
                Route::get('close/{id}', 'support_ticket_close');

            });

        });
        Route::group(['prefix' => 'booking', 'as' => 'booking.'], function () {
            Route::controller(BookingController::class)->group(function () {
                Route::get('get-route', 'get_route');
                Route::post('ride-find', 'find_rides');
                Route::post('apply', 'apply_booking');
                Route::post('confirm', 'confirm_booking');
                Route::get('booking', 'my_booking');
            });
        });

    });

});
