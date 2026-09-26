<?php

use Illuminate\Support\Facades\Route;
use Modules\Venue\Http\Controllers\VenueController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware(['api', 'throttle:60,1'])->prefix('v1')->group(function () {
    Route::get('venues/filters', [VenueController::class, 'filters']);
    Route::get('venues', [VenueController::class, 'index']);
    Route::get('venues/{slug}', [VenueController::class, 'show']);
});
