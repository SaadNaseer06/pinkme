<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogSlowRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = (bool) config('performance.log_enabled', false);
        $thresholdMs = (int) config('performance.slow_request_ms', 800);

        if (! $enabled) {
            return $next($request);
        }

        $start = microtime(true);
        $response = $next($request);
        $elapsedMs = (int) round((microtime(true) - $start) * 1000);

        if ($elapsedMs >= $thresholdMs) {
            Log::warning('slow_request', [
                'duration_ms' => $elapsedMs,
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => optional($request->route())->getName(),
                'status' => $response->getStatusCode(),
                'user_id' => optional($request->user())->id,
            ]);
        }

        return $response;
    }
}
