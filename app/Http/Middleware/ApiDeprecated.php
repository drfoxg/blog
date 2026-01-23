<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiDeprecated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //\Log::info('ApiDeprecated middleware called');

        $response = $next($request);

        $response->headers->set('X-API-Deprecated', 'true');
        $response->headers->set('X-API-Deprecation-Date', config('api_version.deprecation_date'));

        return $response;
    }
}
