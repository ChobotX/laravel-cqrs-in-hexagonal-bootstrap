<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

/**
 * Pre-projected general label for {@see UserGridRow}.
 */
final readonly class UserGridLabel
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
