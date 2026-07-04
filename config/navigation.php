<?php

return [
    'public' => [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Roadmap', 'route' => 'roadmap'],
        ['label' => 'Stack', 'route' => 'stack'],
        ['label' => 'Contact', 'route' => 'contact'],
    ],

    'roles' => [
        'user' => [
            'label' => 'User',
            'description' => 'User workspace for profile, coursework, account security, and final project progress.',
            'abilities' => ['view_cabinet', 'manage_own_profile', 'track_coursework'],
        ],
        'admin' => [
            'label' => 'Admin',
            'description' => 'Operational role for managing user access and protected admin-only coursework tools.',
            'abilities' => ['view_cabinet', 'manage_users'],
        ],
    ],
];
