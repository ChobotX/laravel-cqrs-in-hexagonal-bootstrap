<?php

declare(strict_types=1);

use App\Domain\Team\Team;
use App\Domain\Team\TeamId;
use App\Domain\Team\TeamName;
use App\Domain\Team\TeamSlug;
use App\Infrastructure\Eloquent\Team\EloquentTeamRepository;
use App\Infrastructure\Eloquent\Team\TeamMapper;

function teamRepo(): EloquentTeamRepository
{
    return new EloquentTeamRepository(new TeamMapper);
}

function makeTestTeam(string $id, string $name, string $slug, ?string $parentId = null): Team
{
    return new Team(
        id: new TeamId($id),
        name: new TeamName($name),
        slug: new TeamSlug($slug),
        description: $name.' description',
        parentTeamId: $parentId !== null ? new TeamId($parentId) : null,
    );
}

it('creates and finds a team by id', function (): void {
    $eloquentTeamRepository = teamRepo();
    $team = makeTestTeam('550e8400-e29b-41d4-a716-446655440b00', 'Engineering', 'engineering');

    $eloquentTeamRepository->create($team);
    $found = $eloquentTeamRepository->findById($team->id);

    expect($found)->not->toBeNull()
        ->and($found->name->value)->toBe('Engineering')
        ->and($found->slug->value)->toBe('engineering')
        ->and($found->parentTeamId)->toBeNull();
});

it('finds a team by slug', function (): void {
    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b01', 'Backend', 'backend'));

    $found = $eloquentTeamRepository->findBySlug(new TeamSlug('backend'));

    expect($found)->not->toBeNull()
        ->and($found->name->value)->toBe('Backend');
});

it('returns null for non-existent slug', function (): void {
    $eloquentTeamRepository = teamRepo();

    $found = $eloquentTeamRepository->findBySlug(new TeamSlug('no-such-slug'));

    expect($found)->toBeNull();
});

it('finds all teams', function (): void {
    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b03', 'Team A', 'team-aa'));
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b04', 'Team B', 'team-bb'));

    $all = $eloquentTeamRepository->findAll();

    expect($all)->toHaveCount(2);
});

it('updates a team', function (): void {
    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b05', 'Original', 'original'));

    $team = makeTestTeam('550e8400-e29b-41d4-a716-446655440b05', 'Updated', 'updated');
    $eloquentTeamRepository->update($team);

    $found = $eloquentTeamRepository->findById(new TeamId('550e8400-e29b-41d4-a716-446655440b05'));

    expect($found->name->value)->toBe('Updated')
        ->and($found->slug->value)->toBe('updated');
});

it('soft-deletes a team and reparents children', function (): void {
    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b06', 'Parent', 'parent'));
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b07', 'Child', 'child', '550e8400-e29b-41d4-a716-446655440b06'));

    $eloquentTeamRepository->delete(new TeamId('550e8400-e29b-41d4-a716-446655440b06'));

    $deletedParent = $eloquentTeamRepository->findById(new TeamId('550e8400-e29b-41d4-a716-446655440b06'));
    $child = $eloquentTeamRepository->findById(new TeamId('550e8400-e29b-41d4-a716-446655440b07'));

    expect($deletedParent)->toBeNull()
        ->and($child)->not->toBeNull()
        ->and($child->parentTeamId)->toBeNull();
});

it('creates team with parent', function (): void {
    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b08', 'Parent', 'parent-tm'));

    $team = makeTestTeam('550e8400-e29b-41d4-a716-446655440b09', 'Child', 'child-tm', '550e8400-e29b-41d4-a716-446655440b08');
    $eloquentTeamRepository->create($team);

    $found = $eloquentTeamRepository->findById($team->id);

    expect($found->parentTeamId)->not->toBeNull()
        ->and($found->parentTeamId->value)->toBe('550e8400-e29b-41d4-a716-446655440b08');
});

it('detects cycle on update when setting descendant as parent', function (): void {
    $eloquentTeamRepository = teamRepo();
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b10', 'Grandparent', 'grandparent'));
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b11', 'Parent', 'parent-cy', '550e8400-e29b-41d4-a716-446655440b10'));
    $eloquentTeamRepository->create(makeTestTeam('550e8400-e29b-41d4-a716-446655440b12', 'Child', 'child-cy', '550e8400-e29b-41d4-a716-446655440b11'));

    $team = makeTestTeam('550e8400-e29b-41d4-a716-446655440b10', 'Grandparent', 'grandparent', '550e8400-e29b-41d4-a716-446655440b12');
    $eloquentTeamRepository->update($team);
})->throws(App\Domain\Team\Exception\TeamCycleDetectedException::class);
