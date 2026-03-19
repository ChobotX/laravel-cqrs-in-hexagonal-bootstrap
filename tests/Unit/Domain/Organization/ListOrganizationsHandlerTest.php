<?php

declare(strict_types=1);

use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;
use App\Domain\Organization\Query\ListOrganizations\ListOrganizationsHandler;
use App\Domain\Organization\Query\ListOrganizations\ListOrganizationsQuery;
use Tests\Helper\FakeOrganizationRepository;

it('returns all organizations', function (): void {
    $org1 = new Organization(
        new OrganizationId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationName('Acme Corp'),
        new OrganizationSlug('acme-corp'),
        'Org 1',
    );
    $org2 = new Organization(
        new OrganizationId('660e8400-e29b-41d4-a716-446655440000'),
        new OrganizationName('Beta Inc'),
        new OrganizationSlug('beta-inc'),
        'Org 2',
    );

    $repository = new FakeOrganizationRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $org1,
        '660e8400-e29b-41d4-a716-446655440000' => $org2,
    ]);

    $handler = new ListOrganizationsHandler($repository);

    $result = $handler->handle(new ListOrganizationsQuery);

    expect($result)->toHaveCount(2);
});

it('returns empty when no organizations exist', function (): void {
    $repository = new FakeOrganizationRepository;

    $handler = new ListOrganizationsHandler($repository);

    $result = $handler->handle(new ListOrganizationsQuery);

    expect($result)->toBeEmpty();
});
