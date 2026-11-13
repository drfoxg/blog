<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'blog',
        'status' => 'ok',
        'docs' => '/api/v1',
    ]);
});
