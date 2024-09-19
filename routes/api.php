<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;






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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('users')->group(function (){
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::patch('/reset_password', [UserController::class, 'resetPassword']);
        Route::post('/confirmationEmail', [UserController::class, 'confirmationEmail']);


    });

    Route::prefix('books')->group(function (){
        Route::get('/', [BookController::class, 'index']);
        Route::post('/', [BookController::class, 'store']);
        Route::put('/{id}', [BookController::class, 'update']);
        Route::get('/{id}', [BookController::class, 'show']);
        Route::delete('/{id}', [BookController::class, 'destroy']);
    });

    Route::prefix('students')->group(function (){
        Route::get('/', [StudentController::class, 'index']);
        Route::post('/', [StudentController::class, 'store']);
        Route::put('/{id}', [StudentController::class, 'update']);
        Route::get('/{id}', [StudentController::class, 'show']);
        Route::delete('/{id}', [StudentController::class, 'destroy']);
    });

    Route::prefix('rents')->group(function (){
        Route::get('/', [RentController::class, 'index']);
        Route::post('/', [RentController::class, 'store']);
        Route::put('/{id}', [RentController::class, 'update']);
        Route::get('/{id}', [RentController::class, 'show']);
        Route::delete('/{id}', [RentController::class, 'destroy']);
    });

});
