<?php

declare(strict_types=1);

namespace App\Domain\Organization;

use App\Application\Organization\TenantAgnostic;

#[TenantAgnostic(reason: 'Organization IS the tenant itself')]
final readonly class Organization
{
    public function __construct(
        public OrganizationId $id,
        public OrganizationName $name,
        public OrganizationSlug $slug,
        public string $description,
    ) {}
}
