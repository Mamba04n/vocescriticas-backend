<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$allowedOrigin = rtrim((string) (getenv('FRONTEND_URL') ?: 'https://frontend-nu-nine-65.vercel.app'), '/');
$origin = rtrim((string) ($_SERVER['HTTP_ORIGIN'] ?? ''), '/');

if ($origin !== '' && $origin === $allowedOrigin) {
    header('Access-Control-Allow-Origin: '.$allowedOrigin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Accept, Authorization, Content-Type, Origin, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN');
    header('Vary: Origin');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
