<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\MeasureRequestTime;
use App\Http\Middleware\AntiSpam;
use App\Http\Middleware\ApiDeprecated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__ . '/../routes/web.php', __DIR__ . '/../routes/debug.php'],
        api: [__DIR__ . '/../routes/api.php'],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $proxies = array_filter(array_map(
            'trim',
            explode(',', env('TRUSTED_PROXIES', ''))
        ));

        $middleware->trustProxies(
            empty($proxies) ? null : $proxies,
            Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->alias([
            'deprecated' => ApiDeprecated::class,
            'antispam' => AntiSpam::class,
        ]);

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
            MeasureRequestTime::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
