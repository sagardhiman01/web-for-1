<?php
/*
|--------------------------------------------------------------------------
| PHP Built-in Server Router
|--------------------------------------------------------------------------
| This script allows the PHP built-in server to handle URL rewrites
| like Apache's mod_rewrite, enabling clean URLs for Laravel.
*/

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$filePath = __DIR__ . $path;

// 1. Protection for sensitive files
if (preg_match('/\.env|\.git|core\/|install\//i', $path)) {
    http_response_code(403);
    echo 'Access Denied (Protected System File)';
    exit;
}

// 2. Serve existing files/folders as-is
if ($path !== '/' && is_file($filePath)) {
    return false;
}

// 3. Fake the script name so Laravel handles the route correctly
$_SERVER['SCRIPT_NAME'] = '/index.php';

// 4. Route everything else through index.php
require_once __DIR__ . '/index.php';
