<?php

declare(strict_types=1);

namespace App\Domain\Team;

use App\Domain\Team\Contract\TeamId;

final readonly class Team
{
    public function __construct(
        public TeamId $id,
        public TeamName $name,
        public TeamSlug $slug,
        public string $description,
        public ?TeamId $parentTeamId,
    ) {}
}
