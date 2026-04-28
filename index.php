<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Temporary Review Hold Mode
|--------------------------------------------------------------------------
|
| If the ".review-hold" marker exists in the web root, serve a neutral
| static page for all requests. This is a reversible safety switch for
| registry/abuse review without deleting the application.
|
*/

$reviewHoldMarker = __DIR__ . '/.review-hold';
$reviewHoldPage = __DIR__ . '/review_hold.html';
$requestHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$isProductionDomainRequest = $requestHost !== '' && strpos($requestHost, 'coreasset.store') !== false;

// Normalize tunneled HTTPS requests before Laravel captures the request.
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['REQUEST_SCHEME'] = 'https';
    $_SERVER['SERVER_PORT'] = 443;
}

if ($isProductionDomainRequest && is_file($reviewHoldMarker) && is_file($reviewHoldPage)) {
    http_response_code(200);
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Robots-Tag: noindex, nofollow, noarchive', true);
    readfile($reviewHoldPage);
    exit;
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/

if (file_exists($maintenance = __DIR__ . '/core/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require __DIR__ . '/core/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$app = require_once __DIR__ . '/core/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
