<?php

return [
    'csp' => [
        'allow_vite_dev_server' => env('APP_ENV') === 'local' && (bool) env('APP_DEBUG', false),

        'directives' => [
            'default-src' => ["'none'"],
            'base-uri' => ["'none'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'none'"],
            'object-src' => ["'none'"],
            'script-src' => ["'self'"],
            'style-src' => ["'self'"],
            'img-src' => ["'self'", 'data:', 'https://avatars.githubusercontent.com'],
            'font-src' => ["'self'"],
            'connect-src' => ["'self'"],
            'manifest-src' => ["'self'"],
            'media-src' => ["'self'"],
            'frame-src' => ["'none'"],
            'worker-src' => ["'none'"],
            'upgrade-insecure-requests' => [],
            'block-all-mixed-content' => [],
        ],

        'vite' => [
            'script_src' => ['http://127.0.0.1:5173', 'http://localhost:5173'],
            'style_src' => ['http://127.0.0.1:5173', 'http://localhost:5173'],
            'connect_src' => ['http://127.0.0.1:5173', 'http://localhost:5173', 'ws://127.0.0.1:5173', 'ws://localhost:5173'],
        ],
    ],
];
