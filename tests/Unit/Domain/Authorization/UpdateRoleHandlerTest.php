<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Application\Event\PropertyChangeBuilder;
use App\Domain\Authorization\Constant\RoleFields;
use App\Domain\Authorization\Contract\Command\UpdateRoleCommand;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Event\RoleUpdated;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Handler\Command\UpdateRoleHandler;
use App\Domain\Authorization\ValueObject\RoleName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeRoleRepository;

it('updates a role and emits event', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Old description',
        false,
        [],
    );

    $repository = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateRoleHandler($repository, $eventCollector, new PropertyChangeBuilder);

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
    assert($eventCollector->collected[0] instanceof RoleUpdated);
    $changes = $eventCollector->collected[0]->changes();
    expect($eventCollector->collected[0]->roleId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($changes)->toHaveCount(3)
        ->and($changes[0])->toEqual(new PropertyChange(RoleFields::NAME, 'Editor', 'Senior Editor'))
        ->and($changes[1])->toEqual(new PropertyChange(RoleFields::DESCRIPTION, 'Old description', 'Updated description'))
        ->and($changes[2]->property)->toBe(RoleFields::PERMISSIONS);
});

it('does not save or collect event when data is unchanged', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Same description',
        false,
        [],
    );

    $repository = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateRoleHandler($repository, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateRoleCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Editor',
        description: 'Same description',
        permissions: [],
    ));

    expect($repository->saved)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('throws when role does not exist', function (): void {
    $repository = new FakeRoleRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateRoleHandler($repository, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateRoleCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Editor',
        description: 'description',
        permissions: [],
    ));
})->throws(RoleNotFoundException::class);

it('preserves isSystem from existing role', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Admin'),
        'Admin',
        false,
        [],
    );

    $repository = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateRoleHandler($repository, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateRoleCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Admin Pro',
        description: 'Updated',
        permissions: [],
    ));

    expect($repository->saved[0]->isSystem)->toBeFalse();
});
