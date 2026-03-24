<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\SetPermissionOverride\SetPermissionOverrideCommand;
use App\Domain\Authorization\Command\SetPermissionOverride\SetPermissionOverrideHandler;
use App\Domain\Authorization\Event\PermissionOverrideSet;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserPermissionRepository;

it('sets a permission override and emits event', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new SetPermissionOverrideHandler($userPermRepo, $eventCollector);

    $handler->handle(new SetPermissionOverrideCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        permission: 'users.list.read',
        type: 'grant',
        scope: 'all',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(PermissionOverrideSet::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof PermissionOverrideSet);

    expect($event->userId)->toBe('00000000-0000-0000-0000-000000000010');
    expect($event->permission)->toBe('users.list.read');
    expect($event->type)->toBe('grant');
});

it('handles module-only permission override', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new SetPermissionOverrideHandler($userPermRepo, $eventCollector);

    $handler->handle(new SetPermissionOverrideCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        permission: 'users',
        type: 'deny',
        scope: 'own',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(PermissionOverrideSet::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof PermissionOverrideSet);

    expect($event->permission)->toBe('users');
});

it('handles module.feature permission override', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new SetPermissionOverrideHandler($userPermRepo, $eventCollector);

    $handler->handle(new SetPermissionOverrideCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        permission: 'users.list',
        type: 'grant',
        scope: 'team',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(PermissionOverrideSet::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof PermissionOverrideSet);

    expect($event->permission)->toBe('users.list');
});
