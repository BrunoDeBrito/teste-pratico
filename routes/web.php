<?php

use App\Http\Controllers\Admin\VehiclesController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {

    Route::get('/logout', 'Auth\LoginController@logout');

    //Rota de vehicles
    Route::get('/vehicles', [VehiclesController::class, 'index']);
    Route::get('/vehicles/create', [VehiclesController::class, 'create']);
    Route::post('/vehicles', [VehiclesController::class, 'insert']);
    Route::get('/vehicles/{id}/edit', [VehiclesController::class, 'edit']);
    Route::put('/vehicles', [VehiclesController::class, 'update']);
    Route::delete('/vehicles/{id}', [VehiclesController::class, 'delete']);

});
