<?php

use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LogoController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\SocialAuthController;
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

//user profile routes
Route::middleware(['auth:sanctum'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::post('/', [ProfileController::class, 'update']);
    Route::put('password', [ProfileController::class, 'updatePassword']);
    Route::delete('/', [ProfileController::class, 'destroy']);
});


//google or facebook login route
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);


    
// Admin user routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // Restore route (MUST come before apiResource so it doesn't collide with GET /users/{user})
    Route::post('users/{id}/restore', [AdminUserController::class, 'restore'])
        ->name('users.restore');

    // Standard CRUD routes (index, store, show, update, destroy)
    Route::apiResource('users', AdminUserController::class);
});


// Public
Route::get('/category', [CategoryController::class, 'index']);

// Admin
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('categories', [AdminCategoryController::class, 'index']);
    Route::post('categories', [AdminCategoryController::class, 'store']);
    Route::put('categories/{category}', [AdminCategoryController::class, 'update']);
    Route::delete('categories/{category}', [AdminCategoryController::class, 'destroy']);
});

//Logo routes
Route::get('/logo', [LogoController::class, 'index']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::post('/admin/logo', [LogoController::class, 'store']);

    Route::post('/admin/logo/{logo}', [LogoController::class, 'update']);

    Route::delete('/admin/logo/{logo}', [LogoController::class, 'destroy']);
});


// Public routes — no auth required, by slug (SEO-friendly)
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{article:slug}', [ArticleController::class, 'show']);

// Admin/editor routes — full CRUD, all statuses, by numeric id
Route::middleware(['auth:sanctum', 'role:admin,editor'])->prefix('admin')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::get('/articles/{article:id}', [ArticleController::class, 'show']);
    Route::put('/articles/{article:id}', [ArticleController::class, 'update']);
    Route::patch('/articles/{article:id}', [ArticleController::class, 'update']);
    Route::delete('/articles/{article:id}', [ArticleController::class, 'destroy']);
});