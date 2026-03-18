<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\UpdateRole\UpdateRoleCommand;
use App\Domain\Authorization\Command\UpdateRole\UpdateRoleHandler;
use App\Domain\Authorization\Event\RoleUpdated;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeRoleRepository;

it('updates a role and emits event', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        '00000000-0000-0000-0000-000000000001',
        new RoleName('Editor'),
        'Old description',
        false,
        [],
    );

    $repository = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateRoleHandler($repository, $eventCollector);

    $handler->handle(new UpdateRoleCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Senior Editor',
        description: 'Updated description',
        permissions: [
            ['permission' => 'users.list.read', 'scope' => 'all'],
        ],
    ));

    expect($repository->saved)->toHaveCount(1);
    expect($repository->saved[0]->name->value)->toBe('Senior Editor');
    expect($repository->saved[0]->description)->toBe('Updated description');
    expect($repository->saved[0]->permissions)->toHaveCount(1);
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(RoleUpdated::class);
});

it('throws when role does not exist', function (): void {
    $repository = new FakeRoleRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateRoleHandler($repository, $eventCollector);

    $handler->handle(new UpdateRoleCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Editor',
        description: 'description',
        permissions: [],
    ));
})->throws(RoleNotFoundException::class);

it('preserves isSystem and organizationId from existing role', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        '00000000-0000-0000-0000-000000000001',
        new RoleName('Admin'),
        'Admin',
        false,
        [],
    );

    $repository = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateRoleHandler($repository, $eventCollector);

    $handler->handle(new UpdateRoleCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Admin Pro',
        description: 'Updated',
        permissions: [],
    ));

    expect($repository->saved[0]->organizationId)->toBe('00000000-0000-0000-0000-000000000001');
    expect($repository->saved[0]->isSystem)->toBeFalse();
});
