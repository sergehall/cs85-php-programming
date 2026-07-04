<?php

return [
    'account' => [
        'name' => 'Serge Hall',
        'initials' => 'SH',
        'email' => 'serge.hall.dev@gmail.com',
        'role' => 'User',
        'status' => 'Active foundation',
        'course' => 'CS85 PHP Programming - Summer 2026',
        'user_id' => 'Prepared after authentication',
        'member_since' => 'Summer 2026',
        'focus' => 'Build a portfolio-ready Laravel application while following the CS85 course path.',
    ],

    'navigation' => [
        'user' => [
            ['label' => 'Overview', 'route' => 'cabinet.dashboard'],
            ['label' => 'Profile', 'route' => 'cabinet.profile', 'section' => 'profile'],
            ['label' => 'Coursework', 'route' => 'cabinet.coursework', 'section' => 'coursework'],
            ['label' => 'Security', 'route' => 'cabinet.security', 'section' => 'security'],
            ['label' => 'Activity', 'route' => 'cabinet.activity', 'section' => 'activity'],
        ],

        'admin' => [
            ['label' => 'Admin Overview', 'route' => 'cabinet.admin.dashboard'],
            ['label' => 'Users', 'route' => 'cabinet.admin.users', 'section' => 'users'],
            ['label' => 'Content', 'route' => 'cabinet.admin.content', 'section' => 'content'],
        ],
    ],

    'metrics' => [
        ['label' => 'Course Weeks', 'value' => '6', 'detail' => 'Summer sprint structure'],
        ['label' => 'Prepared Routes', 'value' => '20+', 'detail' => 'Public, auth, user, admin, and legacy pages'],
        ['label' => 'Quality Gate', 'value' => '30+', 'detail' => 'Automated tests currently passing'],
        ['label' => 'Infra Services', 'value' => '4', 'detail' => 'MySQL, Redis, Mailpit, Adminer'],
    ],

    'extension_points' => [
        [
            'name' => 'User cabinet section',
            'description' => 'Add a key under cabinet.sections and a navigation item under cabinet.navigation.user. Routes are registered automatically.',
        ],
        [
            'name' => 'Admin cabinet section',
            'description' => 'Add a key under cabinet.admin.sections and a navigation item under cabinet.navigation.admin. Admin routes are registered automatically.',
        ],
        [
            'name' => 'Dashboard focus card',
            'description' => 'Add an item to cabinet.dashboard.focus to surface a new workflow on the overview page.',
        ],
        [
            'name' => 'Database-backed upgrade',
            'description' => 'Move config data into models, migrations, seeders, policies, and controllers when CS85 reaches CRUD-heavy workflows.',
        ],
    ],

    'dashboard' => [
        'focus' => [
            [
                'title' => 'Profile readiness',
                'status' => 'Prepared',
                'description' => 'Identity, course role, portfolio links, and editable profile fields are grouped into one account surface.',
                'route' => 'cabinet.profile',
            ],
            [
                'title' => 'Coursework control',
                'status' => 'In progress',
                'description' => 'Assignments, labs, notes, and final project milestones are organized for future CRUD workflows.',
                'route' => 'cabinet.coursework',
            ],
            [
                'title' => 'Security foundation',
                'status' => 'Active',
                'description' => 'Password, sessions, roles, GitHub OAuth connection, MFA planning, and audit ideas are staged in one security surface.',
                'route' => 'cabinet.security',
            ],
            [
                'title' => 'Activity evidence',
                'status' => 'Prepared',
                'description' => 'A timeline records important project actions and will later move to database-backed event logs.',
                'route' => 'cabinet.activity',
            ],
        ],
        'activity' => [
            ['time' => 'Now', 'title' => 'Docker local environment ready', 'detail' => 'MySQL, Redis, Mailpit, and Adminer run through persistent Docker Compose services.'],
            ['time' => 'Now', 'title' => 'Authentication milestone', 'detail' => 'Cabinet routes run behind middleware, session auth, and user/admin role checks.'],
            ['time' => 'Later', 'title' => 'AI final project surface', 'detail' => 'OpenAI features will be connected server-side with safe environment configuration.'],
        ],
    ],

    'sections' => [
        'profile' => [
            'eyebrow' => 'User Profile',
            'title' => 'Profile Overview',
            'description' => 'A focused profile workspace for account details, portfolio links, short bio, technical skills, and profile completion signals.',
            'summary' => [
                ['label' => 'Name', 'value' => 'Serge Hall'],
                ['label' => 'Email', 'value' => 'serge.hall.dev@gmail.com'],
                ['label' => 'Course', 'value' => 'CS85 PHP Programming'],
                ['label' => 'Role', 'value' => 'User'],
            ],
            'panels' => [
                ['title' => 'Editable profile fields', 'items' => ['First and last name', 'GitHub and LinkedIn links', 'Short bio', 'Technical skills']],
                ['title' => 'Portfolio links', 'items' => ['GitHub: SergeHall', 'Instagram: @sergioartg', 'Project repository: cs85-php-programming']],
                ['title' => 'Profile persistence', 'items' => ['users table profile columns', 'Form Request validation', 'Authenticated user-only updates']],
            ],
            'tasks' => [
                ['label' => 'Add auth scaffolding', 'status' => 'Done'],
                ['label' => 'Create profile migration', 'status' => 'Done'],
                ['label' => 'Validate profile URLs', 'status' => 'Done'],
            ],
        ],

        'coursework' => [
            'eyebrow' => 'Course Workspace',
            'title' => 'Coursework Control Center',
            'description' => 'Assignments, labs, notes, and the final project are grouped as a real user workspace instead of scattered files.',
            'summary' => [
                ['label' => 'Assignments', 'value' => 'Prepared'],
                ['label' => 'Labs', 'value' => 'Prepared'],
                ['label' => 'Notes', 'value' => 'Prepared'],
                ['label' => 'Final Project', 'value' => 'AI-ready'],
            ],
            'panels' => [
                ['title' => 'Workspace areas', 'items' => ['assignments/', 'labs/', 'notes/', 'projects/', 'final-project/']],
                ['title' => 'Upcoming CRUD models', 'items' => ['Assignment', 'Lab', 'Note', 'Milestone', 'ProjectArtifact']],
                ['title' => 'Learning loop', 'items' => ['Read prompt', 'Plan implementation', 'Build feature', 'Test locally', 'Commit with domain message']],
            ],
            'tasks' => [
                ['label' => 'Create assignment records', 'status' => 'Model later'],
                ['label' => 'Track weekly milestones', 'status' => 'Prepared'],
                ['label' => 'Connect final project AI idea', 'status' => 'Planned'],
            ],
        ],

        'security' => [
            'eyebrow' => 'Account Security',
            'title' => 'Security Foundation',
            'description' => 'A protection center for password login, GitHub OAuth account connection, role checks, sessions, MFA planning, and cabinet-only access.',
            'summary' => [
                ['label' => 'Auth', 'value' => 'Active'],
                ['label' => 'Roles', 'value' => 'Configured'],
                ['label' => 'Policies', 'value' => 'Planned'],
                ['label' => 'Audit', 'value' => 'Prepared'],
            ],
            'panels' => [
                ['title' => 'Current controls', 'items' => ['Session auth protects cabinet routes', 'Admin-only middleware', 'GitHub OAuth account connection', 'CSRF-protected Laravel forms']],
                ['title' => 'MFA boundary', 'items' => ['GitHub MFA is managed in GitHub', 'App MFA planned later', 'Recovery codes planned later', 'Sensitive actions need confirmation later']],
                ['title' => 'Current protection', 'items' => ['GitHub OAuth uses state validation', 'GitHub linking checks account ownership', 'Secrets stay in .env', 'CI blocks hardcoded APP_KEY in workflows']],
            ],
            'tasks' => [
                ['label' => 'Move cabinet behind auth middleware', 'status' => 'Done'],
                ['label' => 'Add GitHub account connection', 'status' => 'Done'],
                ['label' => 'Record sensitive account activity', 'status' => 'Planned'],
            ],
        ],

        'activity' => [
            'eyebrow' => 'Project Activity',
            'title' => 'Activity Timeline',
            'description' => 'A project evidence stream for development milestones, infrastructure actions, and future user events.',
            'summary' => [
                ['label' => 'Source', 'value' => 'Config now'],
                ['label' => 'Storage', 'value' => 'MySQL later'],
                ['label' => 'Scope', 'value' => 'User/Admin'],
                ['label' => 'Use', 'value' => 'Evidence'],
            ],
            'panels' => [
                ['title' => 'Recent project events', 'items' => ['Laravel scaffold created', 'Docker infrastructure added', 'Quality gates configured', 'Cabinet expanded into focused sections']],
                ['title' => 'Future tracked events', 'items' => ['Profile updated', 'Assignment submitted', 'Admin content changed', 'Security setting changed']],
                ['title' => 'Operational value', 'items' => ['Supports debugging', 'Shows project progress', 'Creates portfolio evidence', 'Prepares audit logging']],
            ],
            'tasks' => [
                ['label' => 'Create activity_logs migration', 'status' => 'Planned'],
                ['label' => 'Filter user versus admin events', 'status' => 'Planned'],
                ['label' => 'Render database-backed activity feed', 'status' => 'Upcoming'],
            ],
        ],
    ],

    'admin' => [
        'dashboard' => [
            'summary' => [
                ['label' => 'Users', 'value' => 'Prepared'],
                ['label' => 'Content', 'value' => 'Prepared'],
                ['label' => 'Policies', 'value' => 'Planned'],
            ],
            'sections' => [
                ['title' => 'Users', 'route' => 'cabinet.admin.users', 'status' => 'Prepared', 'description' => 'User accounts, roles, profile status, and access review.'],
                ['title' => 'Content', 'route' => 'cabinet.admin.content', 'status' => 'Prepared', 'description' => 'Assignments, labs, notes, roadmap content, and project pages.'],
            ],
        ],
        'sections' => [
            'users' => [
                'eyebrow' => 'Admin Users',
                'title' => 'User Management',
                'description' => 'Prepared operational surface for user accounts, roles, status review, and future policy-protected edits.',
                'items' => ['Search users', 'Review role', 'Check profile completion', 'Inspect recent activity'],
                'tasks' => ['Add users table fields', 'Create admin middleware', 'Add audit records for role changes'],
            ],
            'content' => [
                'eyebrow' => 'Admin Content',
                'title' => 'Content Management',
                'description' => 'Prepared editorial surface for course modules, assignments, labs, notes, and final project milestones.',
                'items' => ['Manage roadmap entries', 'Publish assignment notes', 'Track lab resources', 'Highlight final project requirements'],
                'tasks' => ['Create content migrations', 'Build CRUD controllers', 'Add validation tests for mutations'],
            ],
        ],
    ],
];
