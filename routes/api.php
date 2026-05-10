<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FileUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Posts
    Route::apiResource('posts', PostController::class);
    
    // Categories
    Route::apiResource('categories', CategoryController::class)->except(['show']);

    // File Upload
    Route::post('/upload', [FileUploadController::class, 'upload']);
    Route::delete('/upload', [FileUploadController::class, 'delete']);
});

// Language switch
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, config('app.supported_locales', ['en', 'bn', 'hi']))) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return response()->json(['success' => true, 'locale' => app()->getLocale()]);
})->where('locale', 'en|bn|hi');
