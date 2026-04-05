<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Command\RemovePermissionOverrideCommand;
use App\Domain\Authorization\Contract\Event\PermissionOverrideRemoved;
use App\Domain\Authorization\Handler\Command\RemovePermissionOverrideHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserPermissionRepository;

it('removes a permission override and emits event', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new RemovePermissionOverrideHandler($userPermRepo, $eventCollector);

    $handler->handle(new RemovePermissionOverrideCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        permission: 'users.list.read',
    ));

    expect($userPermRepo->removedOverrides)->toHaveCount(1)
        ->and($userPermRepo->removedOverrides[0]['userId'])->toBe('00000000-0000-0000-0000-000000000010')
        ->and($userPermRepo->removedOverrides[0]['permission'])->toBe('users.list.read');

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(PermissionOverrideRemoved::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof PermissionOverrideRemoved);

    expect($event->userId)->toBe('00000000-0000-0000-0000-000000000010');
    expect($event->permission)->toBe('users.list.read');
});

it('handles module-only permission removal', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new RemovePermissionOverrideHandler($userPermRepo, $eventCollector);

    $handler->handle(new RemovePermissionOverrideCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        permission: 'users',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(PermissionOverrideRemoved::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof PermissionOverrideRemoved);

    expect($event->permission)->toBe('users');
});

it('handles module.feature permission removal', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new RemovePermissionOverrideHandler($userPermRepo, $eventCollector);

    $handler->handle(new RemovePermissionOverrideCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        permission: 'crm.contacts',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(PermissionOverrideRemoved::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof PermissionOverrideRemoved);

    expect($event->permission)->toBe('crm.contacts');
});
