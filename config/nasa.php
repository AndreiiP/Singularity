<?php

return [
    'api_key'  => env('NASA_API_KEY', 'DEMO_KEY'),
    'base_url' => rtrim(env('NASA_BASE_URL', 'https://api.nasa.gov'), '/'),
    'timeout'  => (int) env('NASA_TIMEOUT', 10),
    'cache_ttl' => 5,
];
