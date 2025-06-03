<?php

use App\Http\Controllers\MonitoringController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/moota-tokens', [MonitoringController::class, 'getTokens']);
Route::get('/moota/bank', [MonitoringController::class, 'getBankData']);
Route::get('/moota/profile', [MonitoringController::class, 'getProfileData']);
Route::get('/moota/balance', [MonitoringController::class, 'getBalanceData']);
