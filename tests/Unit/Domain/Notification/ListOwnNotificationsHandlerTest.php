<?php

declare(strict_types=1);

use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Domain\Notification\Contract\Entity\Notification;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\Query\ListOwnNotificationsQuery;
use App\Domain\Notification\Contract\ValueObject\NotificationId;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\Handler\Query\ListOwnNotificationsHandler;
use App\Domain\Notification\ValueObject\NotificationType;
use Tests\Helper\FakeNotificationRepository;

function createNotificationFixture(string $id, string $recipientId, bool $isRead = false): Notification
{
    return new Notification(
        id: new NotificationId($id),
        recipientId: $recipientId,
        type: new NotificationType('user.welcome'),
        title: 'Test',
        body: 'Body',
        level: NotificationLevel::Info,
        link: null,
        channel: NotificationChannel::InApp,
        isRead: $isRead,
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
        readAt: $isRead ? new DateTimeImmutable('2026-01-15T11:00:00+00:00') : null,
    );
}

it('returns paginated notifications for recipient', function (): void {
    $notification = createNotificationFixture('550e8400-e29b-41d4-a716-446655440001', '660e8400-e29b-41d4-a716-446655440000');
    $n2 = createNotificationFixture('550e8400-e29b-41d4-a716-446655440002', '660e8400-e29b-41d4-a716-446655440000');
    $n3 = createNotificationFixture('550e8400-e29b-41d4-a716-446655440003', '770e8400-e29b-41d4-a716-446655440000');

    $repo = new FakeNotificationRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $notification,
        '550e8400-e29b-41d4-a716-446655440002' => $n2,
        '550e8400-e29b-41d4-a716-446655440003' => $n3,
    ]);

    $handler = new ListOwnNotificationsHandler($repo);

    $query = new ListOwnNotificationsQuery(userId: '660e8400-e29b-41d4-a716-446655440000');
    $paginatedResult = $handler->handle($query);

    expect($paginatedResult->items)->toHaveCount(2)
        ->and($paginatedResult->total)->toBe(2);
});

it('uses default pagination when none provided', function (): void {
    $repo = new FakeNotificationRepository;

    $handler = new ListOwnNotificationsHandler($repo);

    $query = new ListOwnNotificationsQuery(userId: '660e8400-e29b-41d4-a716-446655440000');
    $paginatedResult = $handler->handle($query);

    expect($paginatedResult->pagination->page)->toBe(1)
        ->and($paginatedResult->pagination->perPage)->toBe(15);
});

it('uses provided pagination', function (): void {
    $repo = new FakeNotificationRepository;

    $handler = new ListOwnNotificationsHandler($repo);

    $query = new ListOwnNotificationsQuery(
        userId: '660e8400-e29b-41d4-a716-446655440000',
        pagination: new Pagination(2, 5),
    );
    $paginatedResult = $handler->handle($query);

    expect($paginatedResult->pagination->page)->toBe(2)
        ->and($paginatedResult->pagination->perPage)->toBe(5);
});

it('uses provided sorting', function (): void {
    $notification = createNotificationFixture('550e8400-e29b-41d4-a716-446655440001', '660e8400-e29b-41d4-a716-446655440000');
    $newer = new Notification(
        id: new NotificationId('550e8400-e29b-41d4-a716-446655440002'),
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        type: new NotificationType('user.welcome'),
        title: 'Newer',
        body: 'Body',
        level: NotificationLevel::Info,
        link: null,
        channel: NotificationChannel::InApp,
        isRead: false,
        createdAt: new DateTimeImmutable('2026-01-16T10:00:00+00:00'),
        readAt: null,
    );

    $repo = new FakeNotificationRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $notification,
        '550e8400-e29b-41d4-a716-446655440002' => $newer,
    ]);

    $handler = new ListOwnNotificationsHandler($repo);

    $listOwnNotificationsQuery = new ListOwnNotificationsQuery(
        userId: '660e8400-e29b-41d4-a716-446655440000',
    )->withSorting([new Sorting('created_at', SortDirection::Asc)]);

    $paginatedResult = $handler->handle($listOwnNotificationsQuery);

    expect($paginatedResult->items)->toHaveCount(2)
        ->and($paginatedResult->items[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440001')
        ->and($paginatedResult->items[1]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440002');

    $descQuery = new ListOwnNotificationsQuery(
        userId: '660e8400-e29b-41d4-a716-446655440000',
    )->withSorting([new Sorting('created_at', SortDirection::Desc)]);

    $descResult = $handler->handle($descQuery);

    expect($descResult->items[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440002')
        ->and($descResult->items[1]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440001');
});

it('filters by isRead', function (): void {
    $notification = createNotificationFixture('550e8400-e29b-41d4-a716-446655440001', '660e8400-e29b-41d4-a716-446655440000', false);
    $read = createNotificationFixture('550e8400-e29b-41d4-a716-446655440002', '660e8400-e29b-41d4-a716-446655440000', true);

    $repo = new FakeNotificationRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $notification,
        '550e8400-e29b-41d4-a716-446655440002' => $read,
    ]);

    $handler = new ListOwnNotificationsHandler($repo);

    $query = new ListOwnNotificationsQuery(userId: '660e8400-e29b-41d4-a716-446655440000', isRead: false);
    $paginatedResult = $handler->handle($query);

    expect($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->items[0]->isRead)->toBeFalse();
});
