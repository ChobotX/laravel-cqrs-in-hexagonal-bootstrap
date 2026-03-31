<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Notification;

use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property string $user_id
 * @property string $level
 * @property list<string> $channels
 */
final class NotificationPreferenceModel extends Model
{
    public $incrementing = false;

    protected $table = 'notification_preferences';

    protected $primaryKey;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'level',
        'channels',
    ];

    #[Override]
    public function getKey(): null
    {
        return null;
    }

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'channels' => 'array',
        ];
    }
}
