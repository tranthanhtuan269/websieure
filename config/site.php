<?php

return [
    'name' => env('SITE_NAME', 'Web Sieu Re'),
    'tagline' => env('SITE_TAGLINE', 'Theme website theo chủ đề — giá tốt, giao nhanh'),
    'url' => rtrim(env('APP_URL', 'http://localhost'), '/'),
    'contact_email' => env('SITE_CONTACT_EMAIL', 'contact@websieure.test'),
    'contact_phone' => env('SITE_CONTACT_PHONE', '0900 000 000'),
    'currency' => 'VND',
    'admin_email' => env('ADMIN_EMAIL', 'admin@lamwebre.com'),
    'admin_password' => env('ADMIN_PASSWORD', 'lamwebre.com@'),
];
