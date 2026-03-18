<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\DeleteRole\DeleteRoleCommand;
use App\Domain\Authorization\Command\DeleteRole\DeleteRoleHandler;
use App\Domain\Authorization\Event\RoleDeleted;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeRoleRepository;

it('deletes a role and emits event', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        '00000000-0000-0000-0000-000000000001',
        new RoleName('Editor'),
        'Editor role',
        false,
        [],
    );

    $repository = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteRoleHandler($repository, $eventCollector);

    $handler->handle(new DeleteRoleCommand(id: '550e8400-e29b-41d4-a716-446655440000'));

    expect($repository->deleted)->toContain('550e8400-e29b-41d4-a716-446655440000');
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(RoleDeleted::class);
});

it('throws when role does not exist', function (): void {
    $repository = new FakeRoleRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteRoleHandler($repository, $eventCollector);

    $handler->handle(new DeleteRoleCommand(id: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(RoleNotFoundException::class);
