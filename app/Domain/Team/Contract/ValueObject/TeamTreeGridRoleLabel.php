<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\ValueObject;

/**
 * Pre-projected role identifier + display name pair embedded in a TeamTreeGridMember.
 */
final readonly class TeamTreeGridRoleLabel
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
