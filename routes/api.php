<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Добавьте эти маршруты в routes/api.php
|
*/

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/feedback', [FeedbackController::class, 'send']);
});
