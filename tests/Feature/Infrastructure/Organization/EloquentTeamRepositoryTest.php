<?php

declare(strict_types=1);

use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamName;
use App\Domain\Organization\TeamSlug;
use App\Infrastructure\Eloquent\Organization\EloquentOrganizationRepository;
use App\Infrastructure\Eloquent\Organization\EloquentTeamRepository;
use App\Infrastructure\Eloquent\Organization\OrganizationMapper;
use App\Infrastructure\Eloquent\Organization\TeamMapper;

function teamRepo(): EloquentTeamRepository
{
    return new EloquentTeamRepository(new TeamMapper);
}

function createTeamTestOrg(string $id, string $slug): void
{
    $orgRepo = new EloquentOrganizationRepository(new OrganizationMapper);
    $orgRepo->create(new Organization(
        id: new OrganizationId($id),
        name: new OrganizationName('Test Org'),
        slug: new OrganizationSlug($slug),
        description: 'Test',
    ));
}

function makeTestTeam(string $id, string $orgId, string $name, string $slug, ?string $parentId = null): Team
{
    return new Team(
        id: new TeamId($id),
        organizationId: new OrganizationId($orgId),
        name: new TeamName($name),
        slug: new TeamSlug($slug),
        description: $name.' description',
        parentTeamId: $parentId !== null ? new TeamId($parentId) : null,
    );
}

it('creates and finds a team by id', function (): void {
    $orgId = '550e8400-e29b-41d4-a716-446655440a00';
    createTeamTestOrg($orgId, 'team-test-org-a00');

    $eloquentTeamRepository = teamRepo();
    $team = makeTestTeam('550e8400-e29b-41d4-a716-446655440b00', $orgId, 'Engineering', 'engineering');

    $eloquentTeamRepository->create($team);
    $found = $eloquentTeamRepository->findById($team->id);

    expect($found)->not->toBeNull()
        ->and($found->name->value)->toBe('Engineering')
        ->and($found->slug->value)->toBe('engineering')
        ->and($found->parentTeamId)->toBeNull();
});

it('finds a team by slug in organization', function (): void {
    $orgId = '550e8400-e29b-41d4-a716-446655440a01';
    createTeamTestOrg($orgId, 'team-test-org-a01');

    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b01', $orgId, 'Backend', 'backend'));

    $found = $eloquentTeamRepository->findBySlugInOrganization(new TeamSlug('backend'), new OrganizationId($orgId));

    expect($found)->not->toBeNull()
        ->and($found->name->value)->toBe('Backend');
});

it('returns null for non-existent slug', function (): void {
    $orgId = '550e8400-e29b-41d4-a716-446655440a02';
    createTeamTestOrg($orgId, 'team-test-org-a02');

    $eloquentTeamRepository = teamRepo();

    $found = $eloquentTeamRepository->findBySlugInOrganization(new TeamSlug('no-such-slug'), new OrganizationId($orgId));

    expect($found)->toBeNull();
});

it('finds all teams by organization', function (): void {
    $orgId = '550e8400-e29b-41d4-a716-446655440a03';
    createTeamTestOrg($orgId, 'team-test-org-a03');

    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b03', $orgId, 'Team A', 'team-aa'));
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b04', $orgId, 'Team B', 'team-bb'));

    $all = $eloquentTeamRepository->findAllByOrganization(new OrganizationId($orgId));

    expect($all)->toHaveCount(2);
});

it('updates a team', function (): void {
    $orgId = '550e8400-e29b-41d4-a716-446655440a04';
    createTeamTestOrg($orgId, 'team-test-org-a04');

    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b05', $orgId, 'Original', 'original'));

    $team = makeTestTeam('550e8400-e29b-41d4-a716-446655440b05', $orgId, 'Updated', 'updated');
    $eloquentTeamRepository->update($team);

    $found = $eloquentTeamRepository->findById(new TeamId('550e8400-e29b-41d4-a716-446655440b05'));

    expect($found->name->value)->toBe('Updated')
        ->and($found->slug->value)->toBe('updated');
});

it('soft-deletes a team and reparents children', function (): void {
    $orgId = '550e8400-e29b-41d4-a716-446655440a05';
    createTeamTestOrg($orgId, 'team-test-org-a05');

    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b06', $orgId, 'Parent', 'parent'));
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b07', $orgId, 'Child', 'child', '550e8400-e29b-41d4-a716-446655440b06'));

    $eloquentTeamRepository->delete(new TeamId('550e8400-e29b-41d4-a716-446655440b06'));

    $deletedParent = $eloquentTeamRepository->findById(new TeamId('550e8400-e29b-41d4-a716-446655440b06'));
    $child = $eloquentTeamRepository->findById(new TeamId('550e8400-e29b-41d4-a716-446655440b07'));

    expect($deletedParent)->toBeNull()
        ->and($child)->not->toBeNull()
        ->and($child->parentTeamId)->toBeNull();
});

it('creates team with parent', function (): void {
    $orgId = '550e8400-e29b-41d4-a716-446655440a06';
    createTeamTestOrg($orgId, 'team-test-org-a06');

    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b08', $orgId, 'Parent', 'parent-tm'));

    $team = makeTestTeam('550e8400-e29b-41d4-a716-446655440b09', $orgId, 'Child', 'child-tm', '550e8400-e29b-41d4-a716-446655440b08');
    $eloquentTeamRepository->create($team);

    $found = $eloquentTeamRepository->findById($team->id);

    expect($found->parentTeamId)->not->toBeNull()
        ->and($found->parentTeamId->value)->toBe('550e8400-e29b-41d4-a716-446655440b08');
});

it('detects cycle on update when setting descendant as parent', function (): void {
    $orgId = '550e8400-e29b-41d4-a716-446655440a07';
    createTeamTestOrg($orgId, 'team-test-org-a07');

    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b10', $orgId, 'Grandparent', 'grandparent'));
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b11', $orgId, 'Parent', 'parent-cy', '550e8400-e29b-41d4-a716-446655440b10'));
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b12', $orgId, 'Child', 'child-cy', '550e8400-e29b-41d4-a716-446655440b11'));

    $team = makeTestTeam('550e8400-e29b-41d4-a716-446655440b10', $orgId, 'Grandparent', 'grandparent', '550e8400-e29b-41d4-a716-446655440b12');
    $eloquentTeamRepository->update($team);
})->throws(App\Domain\Organization\Exception\TeamCycleDetectedException::class);
