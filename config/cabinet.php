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
            ['label' => 'Messages', 'route' => 'cabinet.messages', 'section' => 'messages'],
            ['label' => 'Security', 'route' => 'cabinet.security', 'section' => 'security'],
            ['label' => 'Activity', 'route' => 'cabinet.activity', 'section' => 'activity'],
        ],

        'admin' => [
            ['label' => 'Admin Overview', 'route' => 'cabinet.admin.dashboard'],
            ['label' => 'Users', 'route' => 'cabinet.admin.users', 'section' => 'users'],
            ['label' => 'Content', 'route' => 'cabinet.admin.content', 'section' => 'content'],
            ['label' => 'Messages', 'route' => 'cabinet.admin.messages', 'section' => 'messages'],
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
                'description' => 'Password, sessions, roles, GitHub OAuth, MFA, and audit ideas are staged in one security surface.',
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
            'description' => 'A prepared user profile surface inspired by the CS79D cabinet: account summary, portfolio identity, skills, and profile completion signals.',
            'summary' => [
                ['label' => 'Name', 'value' => 'Serge Hall'],
                ['label' => 'Email', 'value' => 'serge.hall.dev@gmail.com'],
                ['label' => 'Course', 'value' => 'CS85 PHP Programming'],
                ['label' => 'Role', 'value' => 'User'],
            ],
            'panels' => [
                ['title' => 'Editable profile fields', 'items' => ['First and last name', 'User ID', 'GitHub and LinkedIn links', 'Short bio', 'Technical skills']],
                ['title' => 'Portfolio links', 'items' => ['GitHub: SergeHall', 'Instagram: @sergioartg', 'Project repository: cs85-php-programming']],
                ['title' => 'Future persistence', 'items' => ['users table', 'profiles table', 'Form Request validation', 'Policy-protected updates']],
            ],
            'tasks' => [
                ['label' => 'Add auth scaffolding', 'status' => 'Done'],
                ['label' => 'Create profile migration', 'status' => 'Upcoming'],
                ['label' => 'Validate profile URLs', 'status' => 'Upcoming'],
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

        'messages' => [
            'eyebrow' => 'Communication',
            'title' => 'Messages and Feedback',
            'description' => 'A prepared inbox for course questions, project feedback, and future contact form submissions.',
            'summary' => [
                ['label' => 'Inbox', 'value' => 'Prepared'],
                ['label' => 'Reviewed', 'value' => 'Planned'],
                ['label' => 'Archived', 'value' => 'Planned'],
                ['label' => 'Notifications', 'value' => 'Mailpit-ready'],
            ],
            'panels' => [
                ['title' => 'Message types', 'items' => ['Course question', 'Assignment feedback', 'Contact request', 'Final project review note']],
                ['title' => 'Future states', 'items' => ['New', 'Reviewed', 'Waiting for response', 'Archived']],
                ['title' => 'Local email flow', 'items' => ['Mailpit captures outgoing mail', 'No real SMTP secrets in local development', 'Admin can review submissions later']],
            ],
            'tasks' => [
                ['label' => 'Add contact form validation', 'status' => 'Upcoming'],
                ['label' => 'Persist messages in MySQL', 'status' => 'Upcoming'],
                ['label' => 'Build admin review filters', 'status' => 'Prepared'],
            ],
        ],

        'security' => [
            'eyebrow' => 'Account Security',
            'title' => 'Security Foundation',
            'description' => 'A protection center for password login, GitHub OAuth, role checks, sessions, MFA planning, and cabinet-only access.',
            'summary' => [
                ['label' => 'Auth', 'value' => 'Active'],
                ['label' => 'Roles', 'value' => 'Configured'],
                ['label' => 'Policies', 'value' => 'Planned'],
                ['label' => 'Audit', 'value' => 'Prepared'],
            ],
            'panels' => [
                ['title' => 'Prepared controls', 'items' => ['Password update workflow', 'Session logout', 'Admin-only middleware', 'User-owned profile policy']],
                ['title' => 'Future advanced controls', 'items' => ['Authenticator app MFA', 'GitHub OAuth connection', 'Recovery codes', 'Security event log']],
                ['title' => 'Current protection', 'items' => ['CSRF-protected Laravel forms', 'Session auth protects cabinet routes', 'GitHub OAuth uses state validation', 'Secrets stay in .env', 'CI blocks hardcoded APP_KEY in workflows']],
            ],
            'tasks' => [
                ['label' => 'Move cabinet behind auth middleware', 'status' => 'Done'],
                ['label' => 'Add role-based policies', 'status' => 'Upcoming'],
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
                ['title' => 'Future tracked events', 'items' => ['Profile updated', 'Assignment submitted', 'Message reviewed', 'Admin content changed']],
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
                ['label' => 'Messages', 'value' => 'Prepared'],
                ['label' => 'Policies', 'value' => 'Planned'],
            ],
            'sections' => [
                ['title' => 'Users', 'route' => 'cabinet.admin.users', 'status' => 'Prepared', 'description' => 'User accounts, roles, profile status, and access review.'],
                ['title' => 'Content', 'route' => 'cabinet.admin.content', 'status' => 'Prepared', 'description' => 'Assignments, labs, notes, roadmap content, and project pages.'],
                ['title' => 'Messages', 'route' => 'cabinet.admin.messages', 'status' => 'Prepared', 'description' => 'Contact requests, feedback, reviewed states, and admin response workflow.'],
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
            'messages' => [
                'eyebrow' => 'Admin Messages',
                'title' => 'Message Review',
                'description' => 'Prepared review queue for user contact requests, course feedback, and project communication.',
                'items' => ['Filter new messages', 'Mark reviewed', 'Archive resolved threads', 'Send local test email through Mailpit'],
                'tasks' => ['Create messages table', 'Add reviewed and archived states', 'Protect status changes with policies'],
            ],
        ],
    ],
];
