<?php

declare(strict_types=1);

use App\Domain\Authorization\Action;
use App\Domain\Authorization\Command\ShareRecord\ShareRecordCommand;
use App\Domain\Authorization\Command\ShareRecord\ShareRecordHandler;
use App\Domain\Authorization\Contract\Event\RecordShared;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeRecordShareRepository;

it('shares a record and emits event', function (): void {
    $recordShareRepo = new FakeRecordShareRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new ShareRecordHandler($recordShareRepo, $eventCollector);

    $handler->handle(new ShareRecordCommand(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
        action: 'read',
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

    expect($event->granteeUserId)->toBe('00000000-0000-0000-0000-000000000010');
    expect($event->resourceType)->toBe('contact');
    expect($event->resourceId)->toBe('00000000-0000-0000-0000-000000000099');
    expect($event->action)->toBe('read');
    expect($event->grantorUserId)->toBe('00000000-0000-0000-0000-000000000001');
});
