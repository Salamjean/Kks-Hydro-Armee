<?php

return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ], // <<--- L'accolade de 'web' se ferme ici

        'superadmin' => [
            'driver' => 'session',
            'provider' => 'superadmins',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'corps' => [
            'driver' => 'session',
            'provider' => 'corps_armes',
        ],

        'personnel_soute' => [ // <<--- MAINTENANT À SON PROPRE NIVEAU
            'driver' => 'session',
            'provider' => 'personnels_soute', // Doit correspondre au nom du provider ci-dessous
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ], // <<--- L'accolade de 'users' se ferme ici

        'superadmins' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\SuperAdmin::class),
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\Admin::class),
        ],

        'corps_armes' => [
            'driver' => 'eloquent',
            'model' => App\Models\CorpsArme::class,
        ],

        'personnels_soute' => [ // <<--- MAINTENANT À SON PROPRE NIVEAU
            'driver' => 'eloquent',
            'model' => App\Models\Personnel::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 300,
            'throttle' => 300,
        ], // <<--- L'accolade de 'users' (pour passwords) se ferme ici

        'personnels_soute' => [ // <<--- MAINTENANT À SON PROPRE NIVEAU
            'provider' => 'personnels_soute',
            'table' => 'password_reset_tokens',
            'expire' => 300,
            'throttle' => 300,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];