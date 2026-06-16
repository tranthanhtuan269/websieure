<?php

return [
    'commission_rate' => (int) env('AFFILIATE_COMMISSION_RATE', 10),
    'cookie_days' => (int) env('AFFILIATE_COOKIE_DAYS', 30),
    'min_payout' => (int) env('AFFILIATE_MIN_PAYOUT', 100000),
    'cookie_name' => 'affiliate_ref',
];
