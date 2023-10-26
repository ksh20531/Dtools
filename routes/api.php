<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusApiController;
use App\Http\Controllers\GolfApiController;


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
Route::get('/getBus', [App\Http\Controllers\BusApiController::class, 'getBus']);
Route::get('/getBookMark', [App\Http\Controllers\BusApiController::class, 'getBookMark']);
Route::put('/bookMark', [App\Http\Controllers\BusApiController::class, 'bookMark']);
Route::delete('/deleteBookMark', [App\Http\Controllers\BusApiController::class, 'deleteBookMark']);


Route::prefix('/golfs')->group(function(){
    Route::get('/', [App\Http\Controllers\GolfApiController::class, 'index']);
    Route::post('/', [App\Http\Controllers\GolfApiController::class, 'store']);
    Route::get('/{golf}', [App\Http\Controllers\GolfApiController::class, 'show']);
    Route::put('/{golf}', [App\Http\Controllers\GolfApiController::class, 'update']);
    Route::delete('/{golf}', [App\Http\Controllers\GolfApiController::class, 'destroy']);
});