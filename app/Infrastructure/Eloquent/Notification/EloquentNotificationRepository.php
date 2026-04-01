<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Notification;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Notification\Contract\NotificationId;
use App\Domain\Notification\Contract\NotificationRepository;
use App\Domain\Notification\Notification;
use App\Domain\Notification\NotificationChannel;
use App\Infrastructure\Eloquent\PaginatesQuery;
use App\Infrastructure\Eloquent\SortsQuery;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;

final readonly class EloquentNotificationRepository implements NotificationRepository
{
    use PaginatesQuery;
    use SortsQuery;

    public function __construct(
        private NotificationMapper $notificationMapper,
    ) {}

    public function create(Notification $notification): void
    {
        $notificationModel = new NotificationModel;
        $notificationModel->id = $notification->id->value;
        $notificationModel->recipient_id = $notification->recipientId;
        $notificationModel->type = $notification->type->value;
        $notificationModel->title = $notification->title;
        $notificationModel->body = $notification->body;
        $notificationModel->level = $notification->level->value;
        $notificationModel->link = $notification->link?->value;
        $notificationModel->channel = $notification->channel->value;
        $notificationModel->is_read = $notification->isRead;
        $notificationModel->save();
    }

    public function findById(NotificationId $notificationId): ?Notification
    {
        $model = NotificationModel::find($notificationId->value);

        if (! $model instanceof NotificationModel) {
            return null;
        }

        return $this->notificationMapper->toDomain($model);
    }

    /** @return PaginatedResult<Notification> */
    public function findByRecipient(
        string $recipientId,
        Pagination $pagination,
        ?bool $isRead = null,
        array $sortings = [],
    ): PaginatedResult {
        $builder = $this->baseQuery($recipientId, $isRead);
        $builder = $this->sortBuilder($builder, $sortings);

        [$models, $total] = $this->paginateBuilder($builder, $pagination);

        return new PaginatedResult(
            array_map($this->notificationMapper->toDomain(...), $models),
            $total,
            $pagination,
        );
    }

    public function markAsRead(NotificationId $notificationId, DateTimeImmutable $readAt): void
    {
        NotificationModel::where('id', $notificationId->value)
            ->update(['is_read' => true, 'read_at' => $readAt->format('Y-m-d H:i:s')]);
    }

    public function markAllAsReadForRecipient(string $recipientId, DateTimeImmutable $readAt): void
    {
        NotificationModel::where('recipient_id', $recipientId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => $readAt->format('Y-m-d H:i:s')]);
    }

    public function delete(NotificationId $notificationId): void
    {
        NotificationModel::where('id', $notificationId->value)->delete();
    }

    public function deleteAllForRecipient(string $recipientId): void
    {
        NotificationModel::where('recipient_id', $recipientId)->delete();
    }

    public function countUnreadByRecipient(string $recipientId): int
    {
        return NotificationModel::where('recipient_id', $recipientId)
            ->where('is_read', false)
            ->count();
    }

    /** @return list<string> */
    private function textSortColumns(): array
    {
        return ['title'];
    }

    /** @return Builder<NotificationModel> */
    private function baseQuery(string $recipientId, ?bool $isRead): Builder
    {
        $query = NotificationModel::where('recipient_id', $recipientId)
            ->where('channel', NotificationChannel::InApp->value);

        if ($isRead !== null) {
            $query->where('is_read', $isRead);
        }

        return $query;
    }
}
