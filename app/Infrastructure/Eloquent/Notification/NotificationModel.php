<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Notification;

use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property string $id
 * @property string $recipient_id
 * @property string $type
 * @property string $title
 * @property string $body
 * @property string $level
 * @property string|null $link
 * @property string $channel
 * @property bool $is_read
 * @property CarbonImmutable|null $read_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class NotificationModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    protected $table = 'notifications';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'recipient_id',
        'type',
        'title',
        'body',
        'level',
        'link',
        'channel',
        'is_read',
        'read_at',
    ];

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'immutable_datetime',
        ];
    }
}
