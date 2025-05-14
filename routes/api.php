<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GpsDeviceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/trial', function (Request $request) {
    dd('oi');
});

Route::post('/pay-loan', [PaymentController::class, 'loanPayment']);

Route::post('/location/update', [LocationController::class, 'update']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('gps-devices/{device_id}/power', [GpsDeviceController::class, 'updatePowerStatus']);
    Route::get('gps-devices/{device_id}/power]', [GpsDeviceController::class, 'getPowerStatus']);
});
