<?php

use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryAdsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FrontPageAdsController;
use App\Http\Controllers\Api\LogoController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Resources\CategoryAdsResource;
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
    Route::post('users/{id}/restore', [AdminUserController::class, 'restore'])
        ->name('users.restore');

    Route::apiResource('users', AdminUserController::class);
});


// Public
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);
Route::get('/categories/{slug}/articles', [CategoryController::class, 'articles']);
Route::get('/categories/{slug}/popular', [CategoryController::class, 'popular']);

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
// routes/api.php

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/featured', [ArticleController::class, 'featured']);
Route::get('/articles/breaking', [ArticleController::class, 'breaking']);
Route::get('/articles/popular', [ArticleController::class, 'popular']);
Route::get('/articles/{slug}', [ArticleController::class, 'show']);

// Admin/editor routes — full CRUD, all statuses, by numeric id
Route::middleware(['auth:sanctum', 'role:admin,editor'])->prefix('admin')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::get('/articles/{article:id}', [ArticleController::class, 'show']);
    Route::put('/articles/{article:id}', [ArticleController::class, 'update']);
    Route::patch('/articles/{article:id}', [ArticleController::class, 'update']);
    Route::delete('/articles/{article:id}', [ArticleController::class, 'destroy']);
});


// Video api routes
Route::get('/video', [VideoController::class, 'index']);        
Route::get('/video/{video:slug}', [VideoController::class, 'show']); 

Route::middleware(['auth:sanctum', 'role:admin,editor'])->group(function () {
    Route::post('/video', [VideoController::class, 'store']);
    Route::put('/video/{video:slug}', [VideoController::class, 'update']);
    Route::delete('/video/{video:slug}', [VideoController::class, 'destroy']);
});

// Public routes — used by the live article/site pages
Route::get('/advertisements', [AdvertisementController::class, 'index']);
Route::get('/advertisements/{advertisement}', [AdvertisementController::class, 'show']);
Route::post('/advertisements/{advertisement}/click', [AdvertisementController::class, 'click']);

// Admin/editor-only routes
Route::middleware(['auth:sanctum', 'role:admin,editor'])->group(function () {
    Route::get('/admin/advertisements', [AdvertisementController::class, 'adminIndex']);
    Route::post('/advertisements', [AdvertisementController::class, 'store']);
    Route::put('/advertisements/{advertisement}', [AdvertisementController::class, 'update']);
    Route::delete('/advertisements/{advertisement}', [AdvertisementController::class, 'destroy']);
});


// Public routes — category page ads
Route::get('/category-ads', [CategoryAdsController::class, 'index']);
Route::get('/category-ads/{ads}', [CategoryAdsController::class, 'show']);
Route::post('/category-ads/{ads}/click', [CategoryAdsResource::class, 'click']);

// Admin/editor-only routes
Route::middleware(['auth:sanctum', 'role:admin,editor'])->group(function () {
    Route::get('/admin/category-ads', [CategoryAdsController::class, 'adminIndex']);
    Route::post('/category-ads', [CategoryAdsController::class, 'store']);
    Route::put('/category-ads/{advertisement}', [CategoryAdsController::class, 'update']);
    Route::delete('/category-ads/{advertisement}', [CategoryAdsController::class, 'destroy']);
});


// Public routes — front page ads
Route::get('/front-ads', [FrontPageAdsController::class, 'index']);
Route::get('/front-ads/{advertisement}', [FrontPageAdsController::class, 'show']);
Route::post('/front-ads/{advertisement}/click',[FrontPageAdsController::class, 'click']);

// Admin/editor-only routes
Route::middleware(['auth:sanctum', 'role:admin,editor'])->group(function () {
    Route::get('/admin/front-ads', [FrontPageAdsController::class, 'adminIndex']);
    Route::post('/front-ads', [FrontPageAdsController::class, 'store']);
    Route::put('/front-ads/{advertisement}', [FrontPageAdsController::class, 'update']);
    Route::delete('/front-ads/{advertisement}', [FrontPageAdsController::class, 'destroy']);
});
