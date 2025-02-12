<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\Users\ProfileController;
use App\Http\Controllers\Api\User\CustomerTicketController;

Route::prefix('user')->as('user.')->group(function () {
    Route::post('login', [AuthController::class, 'userLogin'])->name('login');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('profile-update', [ProfileController::class, 'updateProfile'])->name('profile-update');        Route::post('/update-profile', [AuthController::class, 'userProfileupdate'])->name('userProfileupdate');

        Route::group(['prefix' => 'support-ticket'], function () {
            Route::controller(CustomerTicketController::class)->group(function () {
                Route::post('create', 'SupportTicket');
                Route::get('get', 'get_support_tickets');
                Route::get('conv/{ticket_id}', 'get_support_ticket_conv');
                Route::post('reply/{ticket_id}', 'reply_support_ticket');
                Route::get('close/{id}', 'support_ticket_close');

            });
        });

    });

});
