<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MeasureRequestTime
{
    /**
     * Порог медленного запроса (мс)
     */
    private const SLOW_THRESHOLD = 1000;

    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $startTime) * 1000; // в миллисекундах

        $this->log($request, $response, $duration);

        // Добавляем заголовок с временем (опционально)
        $response->headers->set('X-Response-Time', round($duration, 2) . 'ms');

        return $response;
    }

    private function log(Request $request, Response $response, float $duration): void
    {
        $data = [
            'method'   => $request->method(),
            'uri'      => $request->getRequestUri(),
            'status'   => $response->getStatusCode(),
            'duration' => round($duration, 2) . 'ms',
            'ip'       => $request->ip(),
            'user_id'  => $request->user()?->id,
        ];

        // Медленные запросы — warning
        if ($duration > self::SLOW_THRESHOLD) {
            Log::channel('performance')->warning('Slow request', $data);
            return;
        }

        // Обычные запросы — info
        Log::channel('performance')->info('Request completed', $data);
    }
}
