<?php

use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;



// User Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [UserController::class, 'profile']);
    
    // Allow both PUT and POST methods for profile update
    Route::match(['put', 'patch', 'post'], '/me', [UserController::class, 'updateProfile']);
    
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/logout-all', [UserController::class, 'logoutAllDevices']);
});

//User password reset routes
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');

    
//Admin user routes
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    
    // Restore route (MUST come before apiResource so it doesn't collide with GET /users/{user})
    Route::post('users/{id}/restore', [AdminUserController::class, 'restore'])
        ->name('users.restore');

    // Standard CRUD routes (index, store, show, update, destroy)
    Route::apiResource('users', AdminUserController::class);

});

Route::middleware(['auth:sanctum'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::post('/', [ProfileController::class, 'update']);
    Route::put('password', [ProfileController::class, 'updatePassword']);
    Route::delete('/', [ProfileController::class, 'destroy']);
});