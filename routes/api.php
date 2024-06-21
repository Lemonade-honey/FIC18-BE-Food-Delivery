<?php

use App\Http\Controllers\AuthController;
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

        Route::delete('/logout', [AuthController::class, 'logoutDelete']);
    });
});