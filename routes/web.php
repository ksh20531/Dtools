<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\AlarmController;
use App\Http\Controllers\GolfReservationController;
use App\Http\Controllers\GolfResultController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('/dashboard', DashboardController::class);
Route::get('/getDashboard', [App\Http\Controllers\DashboardController::class, 'list']);

Route::resource('/menu', MenuController::class);
Route::get('/getMenu', [App\Http\Controllers\MenuController::class, 'list']);

Route::resource('/bus', BusController::class)->middleware('auth');;
Route::get('/searchBus', [App\Http\Controllers\BusController::class, 'searchBus']);
Route::get('/selectBus', [App\Http\Controllers\BusController::class, 'selectBus']);
Route::get('/selectStation', [App\Http\Controllers\BusController::class, 'selectStation']);

Route::resource('/alarm', AlarmController::class)->middleware('auth');
Route::get('/getRoutine', [App\Http\Controllers\AlarmController::class, 'getRoutine']);

Route::resource('/golf', GolfReservationController::class);
Route::resource('/result', GolfResultController::class);
