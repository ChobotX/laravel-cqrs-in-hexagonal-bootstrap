<?php

declare(strict_types=1);

namespace App\Domain\Organization;

interface OrganizationRepository
{
    /** @return list<Organization> */
    public function findAll(): array;

    public function findById(OrganizationId $organizationId): ?Organization;

    public function findBySlug(OrganizationSlug $organizationSlug): ?Organization;

    /**
     * @param  list<string>  $ids
     * @return list<Organization>
     */
    public function findByIds(array $ids): array;

    public function create(Organization $organization): void;

    public function update(Organization $organization): void;

    public function delete(OrganizationId $organizationId): void;
}
