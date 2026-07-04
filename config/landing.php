<?php

return [
    'default_affiliate_url' => env('LANDING_DEFAULT_AFFILIATE_URL', env('APP_URL', 'http://localhost') . '/?ref=DEMO'),
    'popup_delay_ms' => (int) env('LANDING_POPUP_DELAY_MS', 800),
    'export_directory' => storage_path('app/exports/landing-pages'),
    'export_zip' => storage_path('app/exports/lamwebre-landing-pages.zip'),
    'ai' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'fallback_models' => [
            'gemini-2.5-flash-lite',
            'gemini-3.5-flash',
            'gemini-flash-latest',
        ],
    ],
    'crawler' => [
        'verify_ssl' => env('LANDING_CRAWLER_VERIFY_SSL', env('APP_ENV') === 'production'),
    ],
    'content' => [
        'locale' => env('LANDING_CONTENT_LOCALE', 'en-US'),
        'language' => env('LANDING_CONTENT_LANGUAGE', 'American English'),
    ],
    'deploy' => [
        'local_enabled' => env('LANDING_DEPLOY_LOCAL_ENABLED', false),
        'local_base_path' => env('LANDING_DEPLOY_LOCAL_BASE', '/var/www'),
    ],
];
