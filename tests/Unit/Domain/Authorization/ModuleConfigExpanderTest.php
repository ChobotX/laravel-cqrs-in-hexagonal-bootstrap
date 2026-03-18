<?php

declare(strict_types=1);

use App\Domain\Authorization\Action;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\ModuleConfigExpander;
use App\Domain\Authorization\PermissionKey;

it('returns empty when feature is not in module config', function (): void {
    $expander = new ModuleConfigExpander;

    $availableModules = [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create']],
            ],
        ],
    ];

    $permissionKey = new PermissionKey(new Module('users'), new Feature('nonexistent'));

    $result = $expander->expandToLeaves($permissionKey, $availableModules);

    expect($result)->toBe([]);
});

it('returns empty when module is not in available modules', function (): void {
    $expander = new ModuleConfigExpander;

    $permissionKey = new PermissionKey(new Module('unknown'));

    $result = $expander->expandToLeaves($permissionKey, []);

    expect($result)->toBe([]);
});

it('expands module-level key to all leaf keys', function (): void {
    $expander = new ModuleConfigExpander;

    $availableModules = [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create']],
                'profile' => ['actions' => ['read', 'update']],
            ],
        ],
    ];

    $permissionKey = new PermissionKey(new Module('users'));

    $result = $expander->expandToLeaves($permissionKey, $availableModules);

    expect($result)->toBe([
        'users.list.read',
        'users.list.create',
        'users.profile.read',
        'users.profile.update',
    ]);
});

it('expands feature-level key to all actions', function (): void {
    $expander = new ModuleConfigExpander;

    $availableModules = [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create', 'delete']],
            ],
        ],
    ];

    $permissionKey = new PermissionKey(new Module('users'), new Feature('list'));

    $result = $expander->expandToLeaves($permissionKey, $availableModules);

    expect($result)->toBe([
        'users.list.read',
        'users.list.create',
        'users.list.delete',
    ]);
});

it('returns single key for action-level permission', function (): void {
    $expander = new ModuleConfigExpander;

    $availableModules = [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create']],
            ],
        ],
    ];

    $permissionKey = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);

    $result = $expander->expandToLeaves($permissionKey, $availableModules);

    expect($result)->toBe(['users.list.read']);
});

it('returns all leaf keys from all modules', function (): void {
    $expander = new ModuleConfigExpander;

    $availableModules = [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read']],
            ],
        ],
        'crm' => [
            'features' => [
                'contacts' => ['actions' => ['read', 'create']],
            ],
        ],
    ];

    $result = $expander->allLeafKeys($availableModules);

    expect($result)->toBe([
        ['users', 'list', 'read'],
        ['crm', 'contacts', 'read'],
        ['crm', 'contacts', 'create'],
    ]);
});
