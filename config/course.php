<?php

return [
    'modules' => [
        [
            'week' => 'Week 1',
            'title' => 'PHP Fundamentals',
            'description' => 'Variables, types, operators, control structures, arrays, and local IDE setup.',
        ],
        [
            'week' => 'Week 2',
            'title' => 'Functions and Object-Oriented PHP',
            'description' => 'Reusable functions, scope, classes, objects, methods, and basic design boundaries.',
        ],
        [
            'week' => 'Week 3',
            'title' => 'Forms, HTTP Requests, and Composer',
            'description' => 'Form handling, request data, validation habits, and dependency management.',
        ],
        [
            'week' => 'Week 4',
            'title' => 'Laravel Routing and Views',
            'description' => 'Routes, Blade templates, layout composition, controllers, and user-facing pages.',
        ],
        [
            'week' => 'Week 5',
            'title' => 'MySQL and Database Operations',
            'description' => 'Schema design, migrations, CRUD workflows, Eloquent models, and query safety.',
        ],
        [
            'week' => 'Week 6',
            'title' => 'Core Features and Final Project Polish',
            'description' => 'Sessions, authorization foundations, production polish, and AI-powered project work.',
        ],
    ],

    'stack' => [
        [
            'category' => 'Application',
            'items' => ['PHP', 'Laravel', 'Blade', 'Vite'],
        ],
        [
            'category' => 'Data',
            'items' => ['SQLite for quick startup', 'MySQL-ready configuration', 'Migrations', 'Seeders'],
        ],
        [
            'category' => 'Quality',
            'items' => ['PHPUnit feature tests', 'Laravel Pint', 'npm audit', 'Build verification'],
        ],
        [
            'category' => 'Future AI',
            'items' => ['OpenAI PHP client', 'Server-side API key handling', 'Final project extension point'],
        ],
    ],

    'contact' => [
        'email' => 'serge.hall.dev@gmail.com',
        'profiles' => [
            [
                'label' => 'Instagram',
                'handle' => '@sergioartg',
                'href' => 'https://www.instagram.com/sergioartg/',
                'description' => 'Visual work, current projects, and creative direction.',
            ],
            [
                'label' => 'GitHub',
                'handle' => 'SergeHall',
                'href' => 'https://github.com/SergeHall',
                'description' => 'Code, backend systems, platform work, and shipping history.',
            ],
        ],
        'topics' => [
            'Course questions and CS85 assignment planning.',
            'Laravel architecture, database design, and backend implementation.',
            'AI-powered final project ideas and responsible API integration.',
            'Portfolio polish, deployment readiness, and GitHub presentation.',
        ],
    ],
];
