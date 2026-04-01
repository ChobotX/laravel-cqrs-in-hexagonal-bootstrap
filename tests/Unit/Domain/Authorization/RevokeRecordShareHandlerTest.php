<?php

declare(strict_types=1);

use App\Domain\Authorization\Action;
use App\Domain\Authorization\Command\RevokeRecordShare\RevokeRecordShareCommand;
use App\Domain\Authorization\Command\RevokeRecordShare\RevokeRecordShareHandler;
use App\Domain\Authorization\Event\RecordShareRevoked;
use App\Domain\Authorization\Exception\RecordShareNotFoundException;
use App\Domain\Authorization\RecordShare;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeRecordShareRepository;

it('revokes a record share and emits event', function (): void {
    $recordShareRepo = new FakeRecordShareRepository;
    $recordShareRepo->shared[] = new RecordShare(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
        action: Action::Read,
        grantorUserId: '00000000-0000-0000-0000-000000000001',
    );
    $eventCollector = new FakeEventCollector;

    $handler = new RevokeRecordShareHandler($recordShareRepo, $eventCollector);

    $handler->handle(new RevokeRecordShareCommand(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
    ));

    expect($recordShareRepo->revoked)->toHaveCount(1);
    expect($recordShareRepo->revoked[0]['granteeUserId'])->toBe('00000000-0000-0000-0000-000000000010');
    expect($recordShareRepo->revoked[0]['resourceType'])->toBe('contact');
    expect($recordShareRepo->revoked[0]['resourceId'])->toBe('00000000-0000-0000-0000-000000000099');
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(RecordShareRevoked::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof RecordShareRevoked);

    expect($event->granteeUserId)->toBe('00000000-0000-0000-0000-000000000010');
    expect($event->resourceType)->toBe('contact');
    expect($event->resourceId)->toBe('00000000-0000-0000-0000-000000000099');
});

it('throws when record share does not exist', function (): void {
    $recordShareRepo = new FakeRecordShareRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new RevokeRecordShareHandler($recordShareRepo, $eventCollector);

    $handler->handle(new RevokeRecordShareCommand(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
    ));
})->throws(RecordShareNotFoundException::class);
