<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\CreateRole\CreateRoleCommand;
use App\Domain\Authorization\Command\CreateRole\CreateRoleHandler;
use App\Domain\Authorization\Event\RoleCreated;
use App\Domain\Authorization\Exception\RoleAlreadyExistsException;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeRoleRepository;

it('creates a role and emits event', function (): void {
    $repository = new FakeRoleRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateRoleHandler($repository, $eventCollector);

    $handler->handle(new CreateRoleCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        organizationId: '00000000-0000-0000-0000-000000000001',
        name: 'Editor',
        description: 'Can edit things',
        permissions: [
            ['permission' => 'users.list.read', 'scope' => 'all'],
            ['permission' => 'users.list.update', 'scope' => 'team'],
        ],
    ));

    expect($repository->saved)->toHaveCount(1);
    expect($repository->saved[0]->name->value)->toBe('Editor');
    expect($repository->saved[0]->permissions)->toHaveCount(2);
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(RoleCreated::class);
});

it('throws when role name already exists in organization', function (): void {
    $existing = new Role(
        new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        '00000000-0000-0000-0000-000000000001',
        new RoleName('Editor'),
        'Existing editor',
        false,
        [],
    );

    $repository = new FakeRoleRepository(['660e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new CreateRoleHandler($repository, $eventCollector);

    $handler->handle(new CreateRoleCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        organizationId: '00000000-0000-0000-0000-000000000001',
        name: 'Editor',
        description: 'Duplicate',
        permissions: [],
    ));
})->throws(RoleAlreadyExistsException::class);

it('allows same name in different organizations', function (): void {
    $existing = new Role(
        new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        '00000000-0000-0000-0000-000000000001',
        new RoleName('Editor'),
        'Existing editor',
        false,
        [],
    );

    $repository = new FakeRoleRepository(['660e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new CreateRoleHandler($repository, $eventCollector);

    $handler->handle(new CreateRoleCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        organizationId: '00000000-0000-0000-0000-000000000002',
        name: 'Editor',
        description: 'Different org',
        permissions: [],
    ));

    expect($repository->saved)->toHaveCount(1);
});
