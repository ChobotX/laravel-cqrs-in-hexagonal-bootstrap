<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Entity;

use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;
use App\Domain\Team\ValueObject\TeamName;

/**
 * Immutable read-model snapshot of a Team returned from queries in the Team context.
 */
final readonly class Team
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public TeamId $id,
        /** Human-visible label or title. */
        public TeamName $name,
        /** Field `slug` for this contract; see module docs for validation rules. */
        public TeamSlug $slug,
        /** Longer human-readable explanation for admin UI or emails. */
        public string $description,
        /** Optional parentTeam identifier when absent. */
        public ?TeamId $parentTeamId,
    ) {}
}
