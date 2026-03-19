<?php

declare(strict_types=1);

use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;
use App\Domain\Organization\Query\GetOrganizationById\GetOrganizationByIdHandler;
use App\Domain\Organization\Query\GetOrganizationById\GetOrganizationByIdQuery;
use Tests\Helper\FakeOrganizationRepository;

it('returns organization when found', function (): void {
    $org = new Organization(
        new OrganizationId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationName('Acme Corp'),
        new OrganizationSlug('acme-corp'),
        'A test org',
    );

    $repository = new FakeOrganizationRepository(['550e8400-e29b-41d4-a716-446655440000' => $org]);

    $handler = new GetOrganizationByIdHandler($repository);

    $organization = $handler->handle(new GetOrganizationByIdQuery('550e8400-e29b-41d4-a716-446655440000'));

    expect($organization->name->value)->toBe('Acme Corp');
});

it('throws when organization not found', function (): void {
    $repository = new FakeOrganizationRepository;

    $handler = new GetOrganizationByIdHandler($repository);

    $handler->handle(new GetOrganizationByIdQuery('550e8400-e29b-41d4-a716-446655440000'));
})->throws(OrganizationNotFoundException::class);
