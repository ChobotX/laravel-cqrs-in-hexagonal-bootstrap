<?php

declare(strict_types=1);

namespace App\Infrastructure\Organization;

use App\Contract\Organization\OrganizationContext;

final readonly class StubOrganizationContext implements OrganizationContext
{
    public function __construct(
        private ?string $defaultOrganizationId,
    ) {}

    public function currentOrganizationId(): ?string
    {
        return $this->defaultOrganizationId;
    }
}
