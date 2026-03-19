<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;

final readonly class OrganizationMapper
{
    public function toDomain(OrganizationModel $organizationModel): Organization
    {
        return new Organization(
            id: new OrganizationId($organizationModel->id),
            name: new OrganizationName($organizationModel->name),
            slug: new OrganizationSlug($organizationModel->slug),
            description: $organizationModel->description,
        );
    }
}
