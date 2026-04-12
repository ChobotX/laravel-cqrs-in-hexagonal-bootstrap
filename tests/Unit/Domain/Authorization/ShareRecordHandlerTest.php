<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Command\ShareRecordCommand;
use App\Domain\Authorization\Contract\Enum\Action;
use App\Domain\Authorization\Contract\Event\RecordShared;
use App\Domain\Authorization\Handler\Command\ShareRecordHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeRecordShareRepository;

it('shares a record with a single action and emits one event', function (): void {
    $recordShareRepo = new FakeRecordShareRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new ShareRecordHandler($recordShareRepo, $eventCollector);

    $handler->handle(new ShareRecordCommand(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
        actions: ['read'],
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($recordShareRepo->shared)->toHaveCount(1);
    expect($recordShareRepo->shared[0]->granteeUserId)->toBe('00000000-0000-0000-0000-000000000010');
    expect($recordShareRepo->shared[0]->resourceType)->toBe('contact');
    expect($recordShareRepo->shared[0]->resourceId)->toBe('00000000-0000-0000-0000-000000000099');
    expect($recordShareRepo->shared[0]->action)->toBe(Action::Read);
    expect($recordShareRepo->shared[0]->grantorUserId)->toBe('00000000-0000-0000-0000-000000000001');
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(RecordShared::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof RecordShared);

    expect($event->action)->toBe('read');
});

it('shares a record with multiple actions in one command', function (): void {
    $recordShareRepo = new FakeRecordShareRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new ShareRecordHandler($recordShareRepo, $eventCollector);

    $handler->handle(new ShareRecordCommand(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'entry',
        resourceId: '00000000-0000-0000-0000-000000000099',
        actions: ['read', 'update'],
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($recordShareRepo->shared)->toHaveCount(2);
    expect($recordShareRepo->shared[0]->action)->toBe(Action::Read);
    expect($recordShareRepo->shared[1]->action)->toBe(Action::Update);
    expect($eventCollector->collected)->toHaveCount(2);
});

it('allows sharing a record with oneself', function (): void {
    $recordShareRepo = new FakeRecordShareRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new ShareRecordHandler($recordShareRepo, $eventCollector);

    $handler->handle(new ShareRecordCommand(
        granteeUserId: '00000000-0000-0000-0000-000000000001',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
        actions: ['read'],
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($recordShareRepo->shared)->toHaveCount(1)
        ->and($recordShareRepo->shared[0]->granteeUserId)->toBe('00000000-0000-0000-0000-000000000001')
        ->and($recordShareRepo->shared[0]->grantorUserId)->toBe('00000000-0000-0000-0000-000000000001');
});

it('emits no events when actions list is empty', function (): void {
    $recordShareRepo = new FakeRecordShareRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new ShareRecordHandler($recordShareRepo, $eventCollector);

    $handler->handle(new ShareRecordCommand(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'entry',
        resourceId: '00000000-0000-0000-0000-000000000099',
        actions: [],
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($recordShareRepo->shared)->toHaveCount(0);
    expect($eventCollector->collected)->toHaveCount(0);
});
