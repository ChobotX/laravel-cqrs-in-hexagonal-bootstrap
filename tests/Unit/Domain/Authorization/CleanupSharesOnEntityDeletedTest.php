<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Enum\Action;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;
use App\Domain\Authorization\EventHandler\CleanupSharesOnEntityDeleted;
use App\Domain\Registry\Contract\Event\EntryDeleted;
use Tests\Helper\FakeRecordShareRepository;

it('revokes all shares for the deleted entity', function (): void {
    $repo = new FakeRecordShareRepository;
    $repo->shared[] = new RecordShare(
        granteeUserId: 'user-1',
        resourceType: 'entry',
        resourceId: '00000000-0000-0000-0000-000000000099',
        action: Action::Read,
        grantorUserId: 'grantor-1',
    );

    $handler = new CleanupSharesOnEntityDeleted($repo);
    $handler->handle(new EntryDeleted(
        entryId: '00000000-0000-0000-0000-000000000099',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($repo->revoked)->toHaveCount(1)
        ->and($repo->revoked[0]['resourceType'])->toBe('entry')
        ->and($repo->revoked[0]['resourceId'])->toBe('00000000-0000-0000-0000-000000000099');
});

it('is a no-op when event does not implement EntityDeleted', function (): void {
    $repo = new FakeRecordShareRepository;

    $nonEntityDeletedEvent = new class implements App\Contract\Event\DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable('2026-01-15T10:00:00+00:00');
        }

        public function entityType(): string
        {
            return 'other';
        }

        public function entityId(): string
        {
            return 'some-id';
        }

        public function actionLabel(): string
        {
            return 'other_event';
        }
    };

    $handler = new CleanupSharesOnEntityDeleted($repo);
    $handler->handle($nonEntityDeletedEvent);

    expect($repo->revoked)->toBeEmpty();
});
