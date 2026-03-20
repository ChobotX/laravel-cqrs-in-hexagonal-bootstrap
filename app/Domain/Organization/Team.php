<?php

declare(strict_types=1);

namespace App\Domain\Organization;

final readonly class Team
{
    public function __construct(
        public TeamId $id,
        public OrganizationId $organizationId,
        public TeamName $name,
        public TeamSlug $slug,
        public string $description,
        public ?TeamId $parentTeamId,
    ) {}
}
