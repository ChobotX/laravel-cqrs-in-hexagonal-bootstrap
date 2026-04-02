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
    ],
    'default_roles' => ['manager', 'team-leader', 'team-member', 'externist'],
];
