<?php

return [
    'enabled' => env(
        'DEBUGBAR_ENABLED',
        env('APP_ENV') === 'local' && (bool) env('APP_DEBUG', false),
    ),
];
