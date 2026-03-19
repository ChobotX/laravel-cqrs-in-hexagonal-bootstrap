<?php

declare(strict_types=1);

use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;
use App\Infrastructure\Eloquent\Organization\EloquentOrganizationRepository;
use App\Infrastructure\Eloquent\Organization\OrganizationMapper;

function orgRepo(): EloquentOrganizationRepository
{
    return new EloquentOrganizationRepository(new OrganizationMapper);
}

function makeTestOrg(string $id, string $name, string $slug): Organization
{
    return new Organization(
        id: new OrganizationId($id),
        name: new OrganizationName($name),
        slug: new OrganizationSlug($slug),
        description: $name.' description',
    );
}

it('creates and finds an organization by id', function (): void {
    $eloquentOrganizationRepository = orgRepo();
    $organization = makeTestOrg('550e8400-e29b-41d4-a716-446655440800', 'Acme Corp', 'acme-corp');

    $eloquentOrganizationRepository->create($organization);
    $found = $eloquentOrganizationRepository->findById($organization->id);

    expect($found)->not->toBeNull()
        ->and($found->name->value)->toBe('Acme Corp')
        ->and($found->slug->value)->toBe('acme-corp');
});

it('finds an organization by slug', function (): void {
    $eloquentOrganizationRepository = orgRepo();
    $eloquentOrganizationRepository->create(makeTestOrg('550e8400-e29b-41d4-a716-446655440801', 'Beta Inc', 'beta-inc'));

    $found = $eloquentOrganizationRepository->findBySlug(new OrganizationSlug('beta-inc'));

    expect($found)->not->toBeNull()
        ->and($found->name->value)->toBe('Beta Inc');
});

it('returns null for non-existent slug', function (): void {
    $eloquentOrganizationRepository = orgRepo();

    $found = $eloquentOrganizationRepository->findBySlug(new OrganizationSlug('no-such-slug'));

    expect($found)->toBeNull();
});

it('finds all organizations', function (): void {
    $eloquentOrganizationRepository = orgRepo();
    $eloquentOrganizationRepository->create(makeTestOrg('550e8400-e29b-41d4-a716-446655440802', 'Org A', 'org-aa'));
    $eloquentOrganizationRepository->create(makeTestOrg('550e8400-e29b-41d4-a716-446655440803', 'Org B', 'org-bb'));

    $all = $eloquentOrganizationRepository->findAll();

    expect($all)->toHaveCount(2);
});

it('finds organizations by ids', function (): void {
    $eloquentOrganizationRepository = orgRepo();
    $eloquentOrganizationRepository->create(makeTestOrg('550e8400-e29b-41d4-a716-446655440804', 'Org X', 'org-xx'));
    $eloquentOrganizationRepository->create(makeTestOrg('550e8400-e29b-41d4-a716-446655440805', 'Org Y', 'org-yy'));
    $eloquentOrganizationRepository->create(makeTestOrg('550e8400-e29b-41d4-a716-446655440806', 'Org Z', 'org-zz'));

    $found = $eloquentOrganizationRepository->findByIds(['550e8400-e29b-41d4-a716-446655440804', '550e8400-e29b-41d4-a716-446655440806']);

    expect($found)->toHaveCount(2);
});

it('updates an organization', function (): void {
    $eloquentOrganizationRepository = orgRepo();
    $eloquentOrganizationRepository->create(makeTestOrg('550e8400-e29b-41d4-a716-446655440807', 'Original', 'original'));

    $updated = new Organization(
        id: new OrganizationId('550e8400-e29b-41d4-a716-446655440807'),
        name: new OrganizationName('Updated'),
        slug: new OrganizationSlug('updated'),
        description: 'Updated description',
    );

    $eloquentOrganizationRepository->update($updated);
    $found = $eloquentOrganizationRepository->findById(new OrganizationId('550e8400-e29b-41d4-a716-446655440807'));

    expect($found->name->value)->toBe('Updated')
        ->and($found->slug->value)->toBe('updated');
});

it('soft-deletes an organization', function (): void {
    $eloquentOrganizationRepository = orgRepo();
    $eloquentOrganizationRepository->create(makeTestOrg('550e8400-e29b-41d4-a716-446655440808', 'ToDelete', 'to-delete'));

    $eloquentOrganizationRepository->delete(new OrganizationId('550e8400-e29b-41d4-a716-446655440808'));

    $found = $eloquentOrganizationRepository->findById(new OrganizationId('550e8400-e29b-41d4-a716-446655440808'));

    expect($found)->toBeNull();
});
