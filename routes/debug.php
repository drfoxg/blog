<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/debug', function (Request $request) {
    return response()->json([
        'ip' => request()->ip(),
        'isFromTrustedProxy' => $request->isFromTrustedProxy(),
        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
        'secure' => $request->secure(), // true если Laravel видит HTTPS
        'scheme' => $request->getScheme(), // http / https
        'url_current' => url()->current(), // текущий URL
        'url_full' => url()->full(), // полный URL с query
        'headers' => [
            'x-forwarded-proto' => $request->header('X-Forwarded-Proto'),
            'x-forwarded-scheme' => $request->header('X-Forwarded-Scheme'),
            'host' => $request->header('Host'),
            'x-forwarded-for' => $request->header('X-Forwarded-For'),
        ],
        // 'server' => $request->server(), // всё что пришло от Nginx/PHP-FPM
    ]);
});

Route::get('/debug/env', function () {
    return [
        'env' => env('TRUSTED_PROXIES'),
        'proxies' => explode(',', env('TRUSTED_PROXIES', '')),
    ];
});

Route::get('/my-ip', function () {
    return request()->ip();
});

Route::get('/debug/jit-check', function () {
    $status = opcache_get_status(false);

    // --- бенчмарк: простой CPU-bound цикл без I/O ---
    $iterations = 100_000;
    $start = hrtime(true);

    $sum = 0;
    for ($i = 0; $i < $iterations; $i++) {
        $sum += $i * 2;
    }

    $elapsedNs = hrtime(true) - $start;
    $elapsedMs = $elapsedNs / 1_000_000;

    if ($status === false) {
        return response()->json([
            'opcache' => 'inactive',
            'message' => 'OPcache is not active (opcache.enable=0 or not loaded)',
            'benchmark' => [
                'iterations' => $iterations,
                'sum'        => $sum,
                'elapsed_ms' => round($elapsedMs, 3),
                'elapsed_ns' => $elapsedNs,
            ],
        ], 503);
    }

    $jit = $status['jit'] ?? [];

    return response()->json([
        'opcache' => [
            'enabled'        => $status['opcache_enabled'] ?? null,
            'memory_used'    => $status['memory_usage']['used_memory'] ?? null,
            'memory_free'    => $status['memory_usage']['free_memory'] ?? null,
            'memory_wasted'  => $status['memory_usage']['wasted_memory'] ?? null,
            'cached_scripts' => $status['opcache_statistics']['num_cached_scripts'] ?? null,
            'hits'           => $status['opcache_statistics']['hits'] ?? null,
            'misses'         => $status['opcache_statistics']['misses'] ?? null,
        ],
        'jit' => [
            'on'          => $jit['on'] ?? null,
            'buffer_size' => $jit['buffer_size'] ?? null,
            'buffer_free' => $jit['buffer_free'] ?? null,
        ],
        'benchmark' => [
            'iterations' => $iterations,
            'sum'        => $sum,
            'elapsed_ms' => round($elapsedMs, 3),
            'elapsed_ns' => $elapsedNs,
        ],
    ]);
});
