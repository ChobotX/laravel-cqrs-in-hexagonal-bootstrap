<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationRepository;
use App\Domain\Organization\OrganizationSlug;

final class FakeOrganizationRepository implements OrganizationRepository
{
    /** @var list<Organization> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, Organization> $organizations */
    public function __construct(
        private array $organizations = [],
    ) {}

    /** @return list<Organization> */
    public function findAll(): array
    {
        return array_values($this->organizations);
    }

    public function findById(OrganizationId $organizationId): ?Organization
    {
        return $this->organizations[$organizationId->value] ?? null;
    }

    public function findBySlug(OrganizationSlug $organizationSlug): ?Organization
    {
        foreach ($this->organizations as $organization) {
            if ($organization->slug->value === $organizationSlug->value) {
                return $organization;
            }
        }

        return null;
    }

    /** @return list<Organization> */
    public function findByIds(array $ids): array
    {
        return array_values(array_filter(
            $this->organizations,
            fn (Organization $organization): bool => in_array($organization->id->value, $ids, true),
        ));
    }

    public function create(Organization $organization): void
    {
        $this->saved[] = $organization;
        $this->organizations[$organization->id->value] = $organization;
    }

    public function update(Organization $organization): void
    {
        $this->saved[] = $organization;
        $this->organizations[$organization->id->value] = $organization;
    }

    public function delete(OrganizationId $organizationId): void
    {
        $this->deleted[] = $organizationId->value;
        unset($this->organizations[$organizationId->value]);
    }
}
