<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RestorantController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function(){
    Route::post('/register', [AuthController::class, 'registerPost']);
    Route::post('/login', [AuthController::class, 'loginPost']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('user')->group(function(){
        Route::get('/', function (Request $request) {
            return [
                'data' => $request->user(),
                'errors' => []
            ];
        });

        Route::patch('/', [UserController::class, 'userUpdatePatch']);

        Route::patch('/role', [UserController::class, 'userRolePatch']);

        Route::delete('/logout', [AuthController::class, 'logoutDelete']);
    });

    Route::prefix('restorant')->group(function(){
        Route::get('/', [RestorantController::class, 'currentRestorant']);

        Route::post('/create', [RestorantController::class, 'createRestorant']);
    });
});