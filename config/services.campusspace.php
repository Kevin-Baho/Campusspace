<?php

// SALIN blok ini ke dalam array return di config/services.php:
return [
    'groq' => [
        'key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        'timeout' => env('GROQ_TIMEOUT', 20),
    ],
    'cron' => ['secret' => env('CRON_SECRET')],
];
