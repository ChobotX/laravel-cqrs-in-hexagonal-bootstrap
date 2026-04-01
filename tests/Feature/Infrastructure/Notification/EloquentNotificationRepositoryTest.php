<?php

declare(strict_types=1);

use App\Application\Pagination\Pagination;
use App\Domain\Notification\Contract\NotificationId;
use App\Domain\Notification\Notification;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationLink;
use App\Domain\Notification\NotificationType;
use App\Infrastructure\Eloquent\Notification\EloquentNotificationRepository;
use App\Infrastructure\Eloquent\Notification\NotificationMapper;
use App\Infrastructure\Eloquent\Notification\NotificationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function createRepoUser(): UserModel
{
    return UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544a000',
        'name' => 'Repo Test User',
        'email' => 'repo-test@test.com',
        'password' => Hash::make('password'),
    ]);
}

function buildNotification(string $id, string $recipientId, ?string $link = null, bool $isRead = false): Notification
{
    return new Notification(
        id: new NotificationId($id),
        recipientId: $recipientId,
        type: new NotificationType('user.welcome'),
        title: 'Welcome!',
        body: 'Welcome to the platform.',
        level: NotificationLevel::Info,
        link: $link !== null ? new NotificationLink($link) : null,
        channel: NotificationChannel::InApp,
        isRead: $isRead,
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
        readAt: $isRead ? new DateTimeImmutable('2026-01-15T11:00:00+00:00') : null,
    );
}

function makeRepo(): EloquentNotificationRepository
{
    return new EloquentNotificationRepository(new NotificationMapper);
}

it('creates and finds notification by id', function (): void {
    $userModel = createRepoUser();
    $eloquentNotificationRepository = makeRepo();
    $notification = buildNotification('550e8400-e29b-41d4-a716-44665544a001', $userModel->id, '/profile');

    $eloquentNotificationRepository->create($notification);

    $found = $eloquentNotificationRepository->findById(new NotificationId('550e8400-e29b-41d4-a716-44665544a001'));
    expect($found)->not->toBeNull()
        ->and($found->id->value)->toBe('550e8400-e29b-41d4-a716-44665544a001')
        ->and($found->link?->value)->toBe('/profile');
});

it('returns null when notification not found', function (): void {
    $eloquentNotificationRepository = makeRepo();
    $found = $eloquentNotificationRepository->findById(new NotificationId('550e8400-e29b-41d4-a716-44665544a099'));

    expect($found)->toBeNull();
});

it('finds by recipient with pagination', function (): void {
    $userModel = createRepoUser();
    $eloquentNotificationRepository = makeRepo();
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a001', $userModel->id));
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a002', $userModel->id));

    $paginatedResult = $eloquentNotificationRepository->findByRecipient($userModel->id, new Pagination(1, 1));

    expect($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->total)->toBe(2);
});

it('filters by isRead', function (): void {
    $userModel = createRepoUser();
    $eloquentNotificationRepository = makeRepo();
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a001', $userModel->id));

    $readNotification = buildNotification('550e8400-e29b-41d4-a716-44665544a002', $userModel->id, null, true);
    NotificationModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544a002',
        'recipient_id' => $userModel->id,
        'type' => 'user.welcome',
        'title' => 'Welcome!',
        'body' => 'Body',
        'level' => 'info',
        'channel' => 'in_app',
        'is_read' => true,
        'read_at' => '2026-01-15 11:00:00',
    ]);

    $paginatedResult = $eloquentNotificationRepository->findByRecipient($userModel->id, new Pagination(1, 15), false);
    expect($paginatedResult->items)->toHaveCount(1);

    $readResult = $eloquentNotificationRepository->findByRecipient($userModel->id, new Pagination(1, 15), true);
    expect($readResult->items)->toHaveCount(1);
});

it('marks notification as read', function (): void {
    $userModel = createRepoUser();
    $eloquentNotificationRepository = makeRepo();
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a001', $userModel->id));

    $eloquentNotificationRepository->markAsRead(
        new NotificationId('550e8400-e29b-41d4-a716-44665544a001'),
        new DateTimeImmutable('2026-01-15T11:00:00+00:00'),
    );

    $model = NotificationModel::find('550e8400-e29b-41d4-a716-44665544a001');
    expect($model->is_read)->toBeTrue()
        ->and($model->read_at)->not->toBeNull();
});

it('marks all as read for recipient', function (): void {
    $userModel = createRepoUser();
    $eloquentNotificationRepository = makeRepo();
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a001', $userModel->id));
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a002', $userModel->id));

    $eloquentNotificationRepository->markAllAsReadForRecipient($userModel->id, new DateTimeImmutable('2026-01-15T11:00:00+00:00'));

    expect(NotificationModel::where('recipient_id', $userModel->id)->where('is_read', false)->count())->toBe(0);
});

it('deletes notification', function (): void {
    $userModel = createRepoUser();
    $eloquentNotificationRepository = makeRepo();
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a001', $userModel->id));

    $eloquentNotificationRepository->delete(new NotificationId('550e8400-e29b-41d4-a716-44665544a001'));

    expect(NotificationModel::find('550e8400-e29b-41d4-a716-44665544a001'))->toBeNull();
});

it('deletes all for recipient', function (): void {
    $userModel = createRepoUser();
    $eloquentNotificationRepository = makeRepo();
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a001', $userModel->id));
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a002', $userModel->id));

    $eloquentNotificationRepository->deleteAllForRecipient($userModel->id);

    expect(NotificationModel::where('recipient_id', $userModel->id)->count())->toBe(0);
});

it('counts unread by recipient', function (): void {
    $userModel = createRepoUser();
    $eloquentNotificationRepository = makeRepo();
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a001', $userModel->id));
    $eloquentNotificationRepository->create(buildNotification('550e8400-e29b-41d4-a716-44665544a002', $userModel->id));

    expect($eloquentNotificationRepository->countUnreadByRecipient($userModel->id))->toBe(2);

    $eloquentNotificationRepository->markAsRead(
        new NotificationId('550e8400-e29b-41d4-a716-44665544a001'),
        new DateTimeImmutable('2026-01-15T11:00:00+00:00'),
    );

    expect($eloquentNotificationRepository->countUnreadByRecipient($userModel->id))->toBe(1);
});
