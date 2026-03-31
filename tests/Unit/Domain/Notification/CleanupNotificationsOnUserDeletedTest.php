<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\Notification\EventHandler\CleanupNotificationsOnUserDeleted;
use App\Domain\User\Event\UserDeleted;
use Tests\Helper\FakeNotificationRepository;

it('deletes all notifications for the deleted user', function (): void {
    $repo = new FakeNotificationRepository;

    $handler = new CleanupNotificationsOnUserDeleted($repo);

    $handler->handle(new UserDeleted(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($repo->deletedAllForRecipients)->toHaveCount(1)
        ->and($repo->deletedAllForRecipients[0])->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('ignores events that do not implement EntityDeleted', function (): void {
    $repo = new FakeNotificationRepository;

    $handler = new CleanupNotificationsOnUserDeleted($repo);

    $nonEntityDeletedEvent = new readonly class(new DateTimeImmutable) implements DomainEvent
    {
        public function __construct(private DateTimeImmutable $occurredAt) {}

        public function occurredAt(): DateTimeImmutable
        {
            return $this->occurredAt;
        }
    };

    $handler->handle($nonEntityDeletedEvent);

    expect($repo->deletedAllForRecipients)->toHaveCount(0);
});
