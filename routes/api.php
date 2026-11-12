<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FeedbackController as FeedbackV1;
use App\Http\Controllers\Api\V2\FeedbackController as FeedbackV2;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\TagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Добавьте эти маршруты в routes/api.php
|
*/

Route::prefix('v1')->middleware(['antispam', 'deprecated', 'throttle:5,1'])->group(function () {
    Route::post('/feedback', [FeedbackV1::class, 'send']);
});

Route::prefix('v2')->middleware(['antispam', 'throttle:5,1'])->group(function () {
    Route::post('/feedback', [FeedbackV2::class, 'send']);
});

Route::prefix('v1')->group(function () {
    Route::apiResource('/posts', PostController::class);
    Route::apiResource('/categories', CategoryController::class);
    Route::apiResource('/tags', TagController::class);
});

/**
* posts
*/
// Route::get('posts', [PostController::class, 'index']);
// Route::post('posts', [PostController::class, 'store']);
// Route::get('posts/{post}', [PostController::class, 'show']);
// Route::patch('posts/{post}', [PostController::class, 'update']);
// Route::delete('posts/{post}', [PostController::class, 'delete']);

/**
* categories
*/
// Route::get('categories', [CategoryController::class, 'index']);
// Route::post('categories', [CategoryController::class, 'store']);
// Route::get('categories/{category}', [CategoryController::class, 'show']);
// Route::patch('categories/{category}', [CategoryController::class, 'update']);
// Route::delete('categories/{category}', [CategoryController::class, 'delete']);


/**
* tags
*/
// Route::get('tags', [TagController::class, 'index']);
// Route::post('tags', [TagController::class, 'store']);
// Route::get('tags/{tag}', [TagController::class, 'show']);
// Route::patch('tags/{tag}', [TagController::class, 'update']);
// Route::delete('tags/{tag}', [TagController::class, 'delete']);
