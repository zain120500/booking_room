<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('booking', [BookingController::class, 'store']);
    Route::post('booking/{id}/memo', [BookingController::class, 'addMemo']);
    Route::get('booking', [BookingController::class, 'index']);
    Route::put('/booking/{id}', [BookingController::class, 'update']);
    Route::delete('/booking/{id}', [BookingController::class, 'destroy']);

    Route::get('/rooms', [RoomController::class, 'index']);       
    Route::get('/rooms/{id}', [RoomController::class, 'show']);   
    Route::post('/rooms', [RoomController::class, 'store']);   
    Route::put('/rooms/{id}', [RoomController::class, 'update']);
    Route::delete('/rooms/{id}', [RoomController::class, 'destroy']); 
});

