<?php

use App\Http\Controllers\Admin\VehiclesController;
use App\Http\Controllers\Auth\{LoginController, RegisterController};
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
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

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::group(['middleware' => ['auth']], function () {

    Route::get('/logout', [LoginController::class, 'logout']);
    Route::get('/register', [RegisterController::class, 'create']);

    //Rota de vehicles
    Route::get('/vehicles', [VehiclesController::class, 'index']);
    Route::get('/vehicles/create', [VehiclesController::class, 'create']);
    Route::post('/vehicles', [VehiclesController::class, 'insert']);
    Route::get('/vehicles/{id}/edit', [VehiclesController::class, 'edit']);
    Route::put('/vehicles', [VehiclesController::class, 'update']);
    Route::delete('/vehicles/{id}', [VehiclesController::class, 'delete']);

    Route::get('/user/{id}/info', [UserController::class, 'info']);

});
