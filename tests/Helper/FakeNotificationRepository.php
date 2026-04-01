<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Notification\Contract\NotificationId;
use App\Domain\Notification\Contract\NotificationRepository;
use App\Domain\Notification\Notification;
use DateTimeImmutable;

final class FakeNotificationRepository implements NotificationRepository
{
    use PaginatesArray;
    use SortsArray;

    /** @var list<Notification> */
    public array $created = [];

    /** @var list<array{id: string, readAt: DateTimeImmutable}> */
    public array $markedAsRead = [];

    /** @var list<string> */
    public array $markedAllAsReadForRecipients = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @var list<string> */
    public array $deletedAllForRecipients = [];

    /** @param array<string, Notification> $notifications */
    public function __construct(
        private array $notifications = [],
    ) {}

    public function create(Notification $notification): void
    {
        $this->created[] = $notification;
        $this->notifications[$notification->id->value] = $notification;
    }

    public function findById(NotificationId $notificationId): ?Notification
    {
        return $this->notifications[$notificationId->value] ?? null;
    }

    /** @return PaginatedResult<Notification> */
    public function findByRecipient(
        string $recipientId,
        Pagination $pagination,
        ?bool $isRead = null,
        array $sortings = [],
    ): PaginatedResult {
        $items = array_values(array_filter(
            $this->notifications,
            static function (Notification $notification) use ($recipientId, $isRead): bool {
                if ($notification->recipientId !== $recipientId) {
                    return false;
                }

                return ! ($isRead !== null && $notification->isRead !== $isRead);
            },
        ));

        $items = $this->sortArray($items, $sortings, $this->sortValueExtractor(...));

        return $this->paginateArray($items, $pagination);
    }

    public function markAsRead(NotificationId $notificationId, DateTimeImmutable $readAt): void
    {
        $this->markedAsRead[] = ['id' => $notificationId->value, 'readAt' => $readAt];
    }

    public function markAllAsReadForRecipient(string $recipientId, DateTimeImmutable $readAt): void
    {
        $this->markedAllAsReadForRecipients[] = $recipientId;
    }

    public function delete(NotificationId $notificationId): void
    {
        $this->deleted[] = $notificationId->value;
        unset($this->notifications[$notificationId->value]);
    }

    public function deleteAllForRecipient(string $recipientId): void
    {
        $this->deletedAllForRecipients[] = $recipientId;
    }

    public function countUnreadByRecipient(string $recipientId): int
    {
        return count(array_filter(
            $this->notifications,
            static fn (Notification $notification): bool => $notification->recipientId === $recipientId && ! $notification->isRead,
        ));
    }

    /** @return callable(Notification): (string|int) */
    private function sortValueExtractor(string $column): callable
    {
        return match ($column) {
            'created_at' => fn (Notification $notification): string => $notification->createdAt->format('Y-m-d H:i:s'),
            default => fn (Notification $notification): int => 0,
        };
    }
}
