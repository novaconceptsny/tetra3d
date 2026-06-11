<?php

return [

    'name' => env('APP_NAME', 'Laravel'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'debug_tour' => (bool) env('DEBUG_TOUR', false),

    'url' => env('APP_URL', 'http://localhost'),
    'url_test' => env('APP_URL_TEST', 'http://localhost:8000'),

    'asset_url' => env('ASSET_URL'),

    'timezone' => 'UTC',

    'locale' => 'en',

    'fallback_locale' => 'en',

    'faker_locale' => 'en_US',

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
    ],

    'aliases' => \Illuminate\Support\Facades\Facade::defaultAliases()->merge([
        'ValidationRules' => \App\Helpers\ValidationRules::class,
    ])->toArray(),

];
