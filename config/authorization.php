<?php

declare(strict_types=1);

return [
    'cache_ttl' => env('AUTH_PERMISSION_CACHE_TTL', 300),
    'super_admin_role' => 'super-admin',
    'modules' => [
        'users' => [
            'label' => 'Users',
            'features' => [
                'list' => [
                    'label' => 'User Management',
                    'actions' => ['read', 'create', 'update', 'delete'],
                ],
                'roles' => [
                    'label' => 'Role Assignment',
                    'actions' => ['read', 'update'],
                ],
            ],
        ],
        'teams' => [
            'label' => 'Teams',
            'features' => [
                'management' => [
                    'label' => 'Team Management',
                    'actions' => ['read', 'create', 'update', 'delete'],
                ],
                'members' => [
                    'label' => 'Team Members',
                    'actions' => ['read', 'update'],
                ],
            ],
        ],
        'labels' => [
            'label' => 'Labels',
            'features' => [
                'management' => [
                    'label' => 'Label Management',
                    'actions' => ['read', 'create'],
                ],
            ],
        ],
        'files' => [
            'label' => 'Files',
            'features' => [
                'storage' => [
                    'label' => 'File Storage',
                    'actions' => ['read', 'upload', 'delete'],
                ],
            ],
        ],
        'registry' => [
            'label' => 'Registry',
            'features' => [
                'definitions' => [
                    'label' => 'Definitions',
                    'actions' => ['read', 'create', 'update', 'delete'],
                ],
                'entries' => [
                    'label' => 'Entries',
                    'actions' => ['read', 'create', 'update', 'delete'],
                ],
            ],
        ],
        'feature_flags' => [
            'label' => 'Feature Flags',
            'features' => [
                'management' => [
                    'label' => 'Feature Flag Management',
                    'actions' => ['read', 'update'],
                ],
            ],
        ],
        'settings' => [
            'label' => 'Settings',
            'features' => [
                'tenant' => [
                    'label' => 'Tenant Settings',
                    'actions' => ['read', 'update'],
                ],
            ],
        ],
        'audit_log' => [
            'label' => 'Audit Log',
            'features' => [
                'history' => [
                    'label' => 'Audit History',
                    'actions' => ['read'],
                ],
            ],
        ],
        'email_templates' => [
            'label' => 'Email Templates',
            'features' => [
                'templates' => [
                    'label' => 'Email Template Management',
                    'actions' => ['read', 'update'],
                ],
                'logs' => [
                    'label' => 'Sent Email Logs',
                    'actions' => ['read'],
                ],
            ],
        ],
    ],
    'default_roles' => ['manager', 'team-leader', 'team-member', 'externist'],
];
