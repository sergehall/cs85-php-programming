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
            'description' => 'Standard cabinet role for profile, coursework, account security, MFA, and personal activity.',
            'abilities' => [
                'view_cabinet',
                'manage_own_profile',
                'track_coursework',
                'manage_own_security',
                'use_application_mfa',
                'request_admin_access',
                'view_own_activity',
            ],
        ],
        'admin' => [
            'label' => 'Admin',
            'description' => 'Administrative cabinet role for user oversight, admin access review, and protected role changes.',
            'abilities' => [
                'view_cabinet',
                'manage_users',
                'review_admin_access_requests',
                'grant_admin_access',
                'revoke_admin_access',
                'view_admin_activity',
                'view_admin_content_workspace',
            ],
        ],
    ],
];
