<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FeedbackController as FeedbackV1;
use App\Http\Controllers\Api\V2\FeedbackController as FeedbackV2;

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

Route::prefix('v2')->middleware('antispam', 'throttle:5,1')->group(function () {
    Route::post('/feedback', [FeedbackV2::class, 'send']);
});
