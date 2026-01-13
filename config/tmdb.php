<?php

return [
    'read_access_token' => env('TMDB_API_READ_ACCESS_TOKEN'),
    'api_key' => env('TMDB_API_KEY'),
    'min_popularity' => (float) env('TMDB_MIN_POPULARITY', 0.4),
    'daily_refresh_count' => (int) env('TMDB_DAILY_REFRESH_COUNT', 17280),
    'refresh_interval_seconds' => (int) env('TMDB_REFRESH_INTERVAL_SECONDS', 5),
];
