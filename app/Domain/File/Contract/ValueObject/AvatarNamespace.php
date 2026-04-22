<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\ValueObject;

/**
 * Canonical namespace key under which user avatar files are stored.
 * Shared across controllers and handlers that dispatch StoreAvatarCommand.
 */
final readonly class AvatarNamespace
{
    public const string VALUE = 'user-avatars';
}
