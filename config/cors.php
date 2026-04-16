<?php

$frontendUrl = rtrim((string) env('FRONTEND_URL', 'https://frontend-nu-nine-65.vercel.app'), '/');

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_origins' => [$frontendUrl],
    'allowed_origins_patterns' => [],
    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'Origin',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
    ],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
