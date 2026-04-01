<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('{tenantSlug}.notifications.{userId}', fn (UserModel $user, string $tenantSlug, string $userId): bool => $user->id === $userId);
