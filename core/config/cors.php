<?php

$csvToArray = static function (?string $value, array $default = []): array {
    $source = $value;
    if ($source === null || trim($source) === '') {
        return $default;
    }

    return array_values(array_filter(array_map('trim', explode(',', $source))));
};

$appUrl = trim((string) env('APP_URL', ''));
$defaultOrigins = [];

if ($appUrl !== '') {
    $defaultOrigins[] = $appUrl;

    $host = parse_url($appUrl, PHP_URL_HOST);
    if (is_string($host) && $host !== '') {
        $defaultOrigins[] = 'https://www.' . ltrim($host, 'www.');
    }
}

$isProduction = env('APP_ENV') === 'production';
if (!$isProduction) {
    $defaultOrigins = array_merge($defaultOrigins, [
        'http://localhost',
        'http://127.0.0.1',
        'http://127.0.0.1:8000',
    ]);
}

$defaultOrigins = array_values(array_unique(array_filter($defaultOrigins)));

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => $csvToArray(env('CORS_PATHS'), ['api/*', 'sanctum/csrf-cookie']),

    'allowed_methods' => $csvToArray(env('CORS_ALLOWED_METHODS'), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']),

    'allowed_origins' => $csvToArray(env('CORS_ALLOWED_ORIGINS'), $defaultOrigins),

    'allowed_origins_patterns' => $csvToArray(env('CORS_ALLOWED_ORIGIN_PATTERNS'), []),

    'allowed_headers' => $csvToArray(env('CORS_ALLOWED_HEADERS'), ['Accept', 'Authorization', 'Content-Type', 'Origin', 'X-Requested-With']),

    'exposed_headers' => $csvToArray(env('CORS_EXPOSED_HEADERS'), []),

    'max_age' => (int) env('CORS_MAX_AGE', 3600),

    'supports_credentials' => filter_var(env('CORS_SUPPORTS_CREDENTIALS', false), FILTER_VALIDATE_BOOL),

];
