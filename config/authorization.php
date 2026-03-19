<?php

declare(strict_types=1);

return [
    'cache_ttl' => env('AUTH_PERMISSION_CACHE_TTL', 300),
    'default_organization_id' => env('DEFAULT_ORGANIZATION_ID'),
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
        'organizations' => [
            'label' => 'Organizations',
            'features' => [
                'management' => [
                    'label' => 'Organization Management',
                    'actions' => ['read', 'create', 'update', 'delete'],
                ],
                'members' => [
                    'label' => 'Member Management',
                    'actions' => ['read', 'update'],
                ],
            ],
        ],
    ],
    'default_roles' => ['admin', 'editor', 'viewer'],
];
