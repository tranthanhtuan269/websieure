<?php

return [
    'default_affiliate_url' => env('LANDING_DEFAULT_AFFILIATE_URL', env('APP_URL', 'http://localhost') . '/?ref=DEMO'),
    'popup_delay_ms' => (int) env('LANDING_POPUP_DELAY_MS', 800),
];
