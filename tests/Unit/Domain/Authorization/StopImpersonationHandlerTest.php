<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\StopImpersonation\StopImpersonationHandler;
use App\Domain\Authorization\Contract\Command\StopImpersonation\StopImpersonationCommand;
use App\Domain\Authorization\Contract\Event\ImpersonationStopped;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeImpersonationManager;

it('stops impersonation and emits event with target user id', function (): void {
    $impersonationManager = new FakeImpersonationManager;
    $impersonationManager->start('00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000002');

    $eventCollector = new FakeEventCollector;

    $handler = new StopImpersonationHandler($impersonationManager, $eventCollector);

    $handler->handle(new StopImpersonationCommand(
        impersonatorId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($impersonationManager->stopped)->toBeTrue();
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(ImpersonationStopped::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof ImpersonationStopped);

    expect($event->impersonatorId)->toBe('00000000-0000-0000-0000-000000000001');
    expect($event->targetUserId)->toBe('00000000-0000-0000-0000-000000000002');
});

it('stops impersonation with empty target when not active', function (): void {
    $impersonationManager = new FakeImpersonationManager;
    $eventCollector = new FakeEventCollector;

    $handler = new StopImpersonationHandler($impersonationManager, $eventCollector);

    $handler->handle(new StopImpersonationCommand(
        impersonatorId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($impersonationManager->stopped)->toBeTrue();
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(ImpersonationStopped::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof ImpersonationStopped);

    expect($event->targetUserId)->toBe('');
});
