<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notifications.{userId}', fn (UserModel $user, string $userId): bool => $user->id === $userId);
