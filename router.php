<?php

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestedFile = __DIR__ . $requestPath;

// Let PHP's built-in server serve existing static files directly.
if ($requestPath !== '/' && is_file($requestedFile)) {
    return false;
}

require __DIR__ . '/index.php';
