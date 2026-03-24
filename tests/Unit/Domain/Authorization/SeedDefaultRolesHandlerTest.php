<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\SeedDefaultRoles\SeedDefaultRolesCommand;
use App\Domain\Authorization\Command\SeedDefaultRoles\SeedDefaultRolesHandler;
use App\Domain\Authorization\Event\DefaultRolesSeeded;
use App\Domain\Authorization\Role;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeRoleRepository;

/** @return array<string, array{features: array<string, array{actions: list<string>}>}> */
function testModules(): array
{
    return [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create', 'update', 'delete']],
            ],
        ],
    ];
}

it('seeds four default roles', function (): void {
    $repository = new FakeRoleRepository;
    $eventCollector = new FakeEventCollector;
    $idGenerator = new FakeIdGenerator;

    $handler = new SeedDefaultRolesHandler(
        $repository,
        $eventCollector,
        $idGenerator,
        testModules(),
    );

    $handler->handle(new SeedDefaultRolesCommand);

    expect($repository->saved)->toHaveCount(4);

    $names = array_map(fn (Role $role): string => $role->name->value, $repository->saved);
    expect($names)->toContain('Admin', 'Editor', 'Member', 'Viewer');
});

it('emits DefaultRolesSeeded event with role ids', function (): void {
    $repository = new FakeRoleRepository;
    $eventCollector = new FakeEventCollector;
    $idGenerator = new FakeIdGenerator;

    $handler = new SeedDefaultRolesHandler(
        $repository,
        $eventCollector,
        $idGenerator,
        testModules(),
    );

    $handler->handle(new SeedDefaultRolesCommand);

    expect($eventCollector->collected)->toHaveCount(1);
    assert($eventCollector->collected[0] instanceof DefaultRolesSeeded);
    expect($eventCollector->collected[0]->roleIds)->toHaveCount(4);
});

it('admin role has all module permissions', function (): void {
    $repository = new FakeRoleRepository;
    $eventCollector = new FakeEventCollector;
    $idGenerator = new FakeIdGenerator;

    $handler = new SeedDefaultRolesHandler(
        $repository,
        $eventCollector,
        $idGenerator,
        testModules(),
    );

    $handler->handle(new SeedDefaultRolesCommand);

    $admin = null;

    foreach ($repository->saved as $role) {
        if ($role->name->value === 'Admin') {
            $admin = $role;
        }
    }

    assert($admin !== null);

    expect($admin->permissions)->toHaveCount(1)
        ->and((string) $admin->permissions[0]->permissionKey)->toBe('users');
});

it('viewer role has only read permissions', function (): void {
    $repository = new FakeRoleRepository;
    $eventCollector = new FakeEventCollector;
    $idGenerator = new FakeIdGenerator;

    $handler = new SeedDefaultRolesHandler(
        $repository,
        $eventCollector,
        $idGenerator,
        testModules(),
    );

    $handler->handle(new SeedDefaultRolesCommand);

    $viewer = null;

    foreach ($repository->saved as $role) {
        if ($role->name->value === 'Viewer') {
            $viewer = $role;
        }
    }

    assert($viewer !== null);

    expect($viewer->permissions)->toHaveCount(1)
        ->and((string) $viewer->permissions[0]->permissionKey)->toBe('users.list.read');
});
