<?php

return [
    'public' => [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Roadmap', 'route' => 'roadmap'],
        ['label' => 'Stack', 'route' => 'stack'],
        ['label' => 'Contact', 'route' => 'contact'],
    ],

    'cabinet' => [
        ['label' => 'Overview', 'route' => 'cabinet.dashboard'],
        ['label' => 'Profile', 'route' => 'cabinet.profile'],
        ['label' => 'Coursework', 'route' => 'cabinet.coursework'],
        ['label' => 'Messages', 'route' => 'cabinet.messages'],
        ['label' => 'Security', 'route' => 'cabinet.security'],
        ['label' => 'Activity', 'route' => 'cabinet.activity'],
    ],

    'cabinet_admin' => [
        ['label' => 'Admin Overview', 'route' => 'cabinet.admin.dashboard'],
        ['label' => 'Users', 'route' => 'cabinet.admin.users'],
        ['label' => 'Content', 'route' => 'cabinet.admin.content'],
        ['label' => 'Messages', 'route' => 'cabinet.admin.messages'],
    ],

    'roles' => [
        'user' => [
            'label' => 'User',
            'description' => 'Learner workspace for profile, coursework, messages, and final project progress.',
            'abilities' => ['view_cabinet', 'manage_own_profile', 'track_coursework', 'send_messages'],
        ],
        'admin' => [
            'label' => 'Admin',
            'description' => 'Operational role for managing users, content, messages, and future moderation workflows.',
            'abilities' => ['view_cabinet', 'manage_users', 'manage_content', 'review_messages'],
        ],
    ],
];
