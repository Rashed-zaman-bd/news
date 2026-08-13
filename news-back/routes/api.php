<?php

use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    
    // Restore route (MUST come before apiResource so it doesn't collide with GET /users/{user})
    Route::post('users/{id}/restore', [UserController::class, 'restore'])
        ->name('users.restore');

    // Standard CRUD routes (index, store, show, update, destroy)
    Route::apiResource('users', UserController::class);

});

Route::middleware(['auth:sanctum'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::post('/', [ProfileController::class, 'update']);
    Route::put('password', [ProfileController::class, 'updatePassword']);
    Route::delete('/', [ProfileController::class, 'destroy']);
});