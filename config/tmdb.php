<?php

return [
    'read_access_token' => env('TMDB_API_READ_ACCESS_TOKEN'),
    'api_key' => env('TMDB_API_KEY'),
    'min_popularity' => (float) env('TMDB_MIN_POPULARITY', 0.4),
];
