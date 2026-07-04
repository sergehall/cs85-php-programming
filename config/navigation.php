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
            'description' => 'Standard student workspace role for personal profile management, coursework access, account security, MFA, and personal activity history.',
            'capabilities' => [
                'Open the cabinet overview, profile, coursework, security, and activity pages.',
                'Edit personal identity fields, portfolio links, bio, skills, and profile photo URL.',
                'Connect GitHub, enable application MFA, and request admin access from the security page.',
                'Review personal activity evidence without access to other users or admin tools.',
            ],
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
            'description' => 'Protected administrative role for user oversight, admin access review, login access control, role changes, and administrative activity review.',
            'capabilities' => [
                'Open the admin overview and user management workspace.',
                'Review pending admin access requests and grant or revoke admin privileges.',
                'Allow or block user login access without deleting the user account.',
                'Review administrative activity signals while coursework remains in the shared coursework page.',
            ],
            'abilities' => [
                'view_cabinet',
                'manage_users',
                'review_admin_access_requests',
                'grant_admin_access',
                'revoke_admin_access',
                'manage_user_login_access',
                'view_admin_activity',
            ],
        ],
    ],
];
