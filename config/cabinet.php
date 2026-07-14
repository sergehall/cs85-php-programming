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
            ['label' => 'AI Assistant', 'route' => 'cabinet.ai', 'section' => 'ai'],
        ],

        'admin' => [
            ['label' => 'Admin Overview', 'route' => 'cabinet.admin.dashboard'],
            ['label' => 'Users', 'route' => 'cabinet.admin.users', 'section' => 'users'],
        ],
    ],

    'metrics' => [
        ['label' => 'Course Weeks', 'value' => '6', 'detail' => 'Summer sprint structure'],
        ['label' => 'Tracked Areas', 'value' => '5', 'detail' => 'Profile, coursework, security, activity, AI'],
        ['label' => 'Quality Gate', 'value' => '100+', 'detail' => 'Automated tests currently passing'],
        ['label' => 'Security Controls', 'value' => 'MFA', 'detail' => 'GitHub OAuth, roles, sessions, and app MFA'],
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
                'status' => 'Active',
                'description' => 'Identity, portfolio links, skills, bio, GitHub avatar fallback, and custom photo URL are managed in one account surface.',
                'route' => 'cabinet.profile',
            ],
            [
                'title' => 'Coursework control',
                'status' => 'Linked',
                'description' => 'The coursework cabinet points to real assignment routes while source files stay outside the public web root.',
                'route' => 'cabinet.coursework',
            ],
            [
                'title' => 'Security foundation',
                'status' => 'Active',
                'description' => 'Password login, sessions, roles, GitHub OAuth connection, app MFA, and admin access requests are handled in one security surface.',
                'route' => 'cabinet.security',
            ],
            [
                'title' => 'Activity evidence',
                'status' => 'Logged',
                'description' => 'A database-backed timeline records profile, coursework, security, MFA, and admin role events with five-at-a-time browsing.',
                'route' => 'cabinet.activity',
            ],
            [
                'title' => 'Local AI learning',
                'status' => 'Streaming',
                'description' => 'Multi-turn tutoring, coding, and architecture conversations run through local LM Studio models.',
                'route' => 'cabinet.ai',
            ],
        ],
        'activity' => [
            ['time' => 'Now', 'title' => 'Profile identity is editable', 'detail' => 'Name, portfolio links, bio, skills, and profile photo URL are stored on the user account.'],
            ['time' => 'Now', 'title' => 'Application MFA is available', 'detail' => 'Authenticator app setup uses QR codes, encrypted secrets, and one-time recovery codes.'],
            ['time' => 'Now', 'title' => 'Activity timeline is database-backed', 'detail' => 'User and admin events are recorded with paginated activity browsing.'],
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
            'description' => 'A protection center for password login, GitHub OAuth account connection, role checks, sessions, application MFA, and cabinet-only access.',
            'summary' => [
                ['label' => 'Auth', 'value' => 'Active'],
                ['label' => 'Roles', 'value' => 'Configured'],
                ['label' => 'Policies', 'value' => 'Planned'],
                ['label' => 'Audit', 'value' => 'Prepared'],
            ],
            'panels' => [
                ['title' => 'Current controls', 'items' => ['Session auth protects cabinet routes', 'Admin-only middleware', 'GitHub OAuth account connection', 'CSRF-protected Laravel forms']],
                ['title' => 'Application MFA', 'items' => ['Authenticator app codes protect cabinet login', 'Recovery codes are generated during setup', 'MFA secrets are encrypted at rest', 'Sensitive actions need confirmation later']],
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
            'description' => 'A database-backed evidence stream for user profile, coursework, security, and admin role events.',
            'summary' => [
                ['label' => 'Source', 'value' => 'Real events'],
                ['label' => 'Storage', 'value' => 'MySQL'],
                ['label' => 'Scope', 'value' => 'User/Admin'],
                ['label' => 'Use', 'value' => 'Evidence'],
            ],
            'panels' => [
                ['title' => 'Tracked user events', 'items' => ['Account registered', 'Profile updated', 'Coursework workspace opened', 'GitHub connected']],
                ['title' => 'Tracked admin events', 'items' => ['Admin access requested', 'Admin access granted', 'Admin access revoked', 'Sensitive role changes']],
                ['title' => 'Operational value', 'items' => ['Supports debugging', 'Shows project progress', 'Creates portfolio evidence', 'Prepares audit logging']],
            ],
            'tasks' => [
                ['label' => 'Create activity_logs migration', 'status' => 'Done'],
                ['label' => 'Filter user versus admin events', 'status' => 'Done'],
                ['label' => 'Render database-backed activity feed', 'status' => 'Done'],
            ],
        ],

        'ai' => [
            'eyebrow' => 'Local AI Platform',
            'title' => 'AI Learning Assistant',
            'description' => 'Private multi-turn conversations routed to specialized local models through LM Studio.',
            'summary' => [
                ['label' => 'Provider', 'value' => 'LM Studio'],
                ['label' => 'History', 'value' => 'Multi-turn'],
                ['label' => 'Delivery', 'value' => 'Streaming'],
                ['label' => 'Access', 'value' => 'User/Admin'],
            ],
            'panels' => [
                ['title' => 'Learning modes', 'items' => ['General tutor', 'Coding assistant', 'Architecture advisor']],
                ['title' => 'Local-first', 'items' => ['Server-side provider calls', 'Database conversation history', 'No cloud fallback']],
                ['title' => 'Safety boundaries', 'items' => ['Escaped output', 'Rate limiting', 'Read-only allowlisted course tools']],
            ],
            'tasks' => [
                ['label' => 'Select a learning mode', 'status' => 'Ready'],
                ['label' => 'Start a conversation', 'status' => 'Ready'],
                ['label' => 'Load the configured LM Studio model', 'status' => 'Local setup'],
            ],
        ],
    ],

    'admin' => [
        'dashboard' => [
            'summary' => [
                ['label' => 'Users', 'value' => 'Prepared'],
                ['label' => 'Access', 'value' => 'Live'],
                ['label' => 'Security', 'value' => 'Active'],
            ],
            'sections' => [
                ['title' => 'Users', 'route' => 'cabinet.admin.users', 'status' => 'Live', 'description' => 'User accounts, roles, login access, profile status, and access review.'],
            ],
        ],
        'sections' => [
            'users' => [
                'eyebrow' => 'Admin Users',
                'title' => 'User Management',
                'description' => 'Operational surface for user accounts, admin access requests, role review, and protected role changes.',
                'items' => ['Review admin access requests', 'Grant admin role', 'Revoke admin role', 'Check profile identity'],
                'tasks' => ['Add audit records for role changes', 'Add searchable user filters', 'Require confirmation for sensitive role actions'],
            ],
        ],
    ],
];
