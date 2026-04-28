<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Frontend Content Cache (seconds)
    |--------------------------------------------------------------------------
    |
    | Caches homepage/page section content loaded via getContent(). Keep this
    | value low enough so admin content updates are reflected quickly.
    |
    */
    'frontend_cache_seconds' => (int) env('FRONTEND_CACHE_SECONDS', 120),

    /*
    |--------------------------------------------------------------------------
    | Language List Cache (seconds)
    |--------------------------------------------------------------------------
    */
    'language_cache_seconds' => (int) env('LANGUAGE_CACHE_SECONDS', 3600),

    /*
    |--------------------------------------------------------------------------
    | SEO Block Cache (seconds)
    |--------------------------------------------------------------------------
    */
    'seo_cache_seconds' => (int) env('SEO_CACHE_SECONDS', 1800),
];

