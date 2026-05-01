<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Slow request/query instrumentation
    |--------------------------------------------------------------------------
    |
    | Enable in production only when troubleshooting:
    | PERFORMANCE_LOG_ENABLED=true
    |
    */
    'log_enabled' => (bool) env('PERFORMANCE_LOG_ENABLED', false),

    // Log requests slower than this threshold.
    'slow_request_ms' => (int) env('PERFORMANCE_SLOW_REQUEST_MS', 800),

    // Log individual SQL queries slower than this threshold.
    'slow_query_ms' => (int) env('PERFORMANCE_SLOW_QUERY_MS', 120),
];
