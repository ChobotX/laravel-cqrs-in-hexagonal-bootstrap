<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

/**
 * Pre-projected team label for {@see UserGridRow}.
 */
final readonly class UserGridTeamLabel
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
