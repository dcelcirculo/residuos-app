<?php

/*
|--------------------------------------------------------------------------
| Configuración principal de la aplicación EcoGestión
|--------------------------------------------------------------------------
| Este archivo define valores globales como:
| - nombre de la app (APP_NAME)
| - entorno (APP_ENV), debug, URL base
| - zona horaria, locale, llaves de cifrado
| La mayor parte son leídas desde el archivo .env
*/

return [

    'name' => env('APP_NAME', 'EcoGestión'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(explode(',', (string) env('APP_PREVIOUS_KEYS', ''))),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];