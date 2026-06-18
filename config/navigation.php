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
