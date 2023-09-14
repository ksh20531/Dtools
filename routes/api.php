<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/searchBus', [App\Http\Controllers\BusApiController::class, 'searchBus']);
Route::get('/selectBus', [App\Http\Controllers\BusApiController::class, 'selectBus']);
Route::get('/selectStation', [App\Http\Controllers\BusApiController::class, 'selectStation']);
