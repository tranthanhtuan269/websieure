<?php

return [
    'hosting_fee' => (int) env('PROVISIONING_HOSTING_FEE', 500000),

    'auto_start' => env('PROVISIONING_AUTO_START', true),

    'server_ip' => env('PROVISIONING_SERVER_IP', '178.104.222.35'),

    'sites_path' => env('PROVISIONING_SITES_PATH', '/var/www'),

    'run_local' => env('PROVISIONING_RUN_LOCAL', false),

    'ssh' => [
        'host' => env('PROVISIONING_SSH_HOST', '178.104.222.35'),
        'user' => env('PROVISIONING_SSH_USER', 'root'),
        'port' => (int) env('PROVISIONING_SSH_PORT', 22),
        'key' => env('PROVISIONING_SSH_KEY'),
        'password' => env('PROVISIONING_SSH_PASSWORD'),
    ],

    'cloudflare' => [
        'api_token' => env('CLOUDFLARE_API_TOKEN'),
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
        'enabled' => env('CLOUDFLARE_ENABLED', false),
    ],

    'wordpress' => [
        'admin_user' => env('PROVISIONING_WP_ADMIN_USER', 'admin'),
    ],
];
