<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

/**
 * Pre-projected role label for {@see UserGridRow}.
 */
final readonly class UserGridRoleLabel
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $isSystem,
    ) {}
}
