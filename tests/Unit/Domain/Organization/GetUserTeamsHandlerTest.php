<?php

declare(strict_types=1);

use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\Query\GetUserTeams\GetUserTeamsHandler;
use App\Domain\Organization\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamName;
use App\Domain\Organization\TeamSlug;
use Tests\Helper\FakeTeamMemberRepository;
use Tests\Helper\FakeTeamRepository;

it('returns user teams in organization', function (): void {
    $orgId = '660e8400-e29b-41d4-a716-446655440000';
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationId($orgId),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $teamMemberRepo = new FakeTeamMemberRepository(
        memberships: ['user-1' => ['550e8400-e29b-41d4-a716-446655440000']],
        teamOrganizations: ['550e8400-e29b-41d4-a716-446655440000' => $orgId],
    );

    $handler = new GetUserTeamsHandler($teamMemberRepo, $teamRepo);

    $result = $handler->handle(new GetUserTeamsQuery('user-1', $orgId));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Engineering');
});

it('returns empty when user has no teams', function (): void {
    $teamRepo = new FakeTeamRepository;
    $teamMemberRepo = new FakeTeamMemberRepository;

    $handler = new GetUserTeamsHandler($teamMemberRepo, $teamRepo);

    $result = $handler->handle(new GetUserTeamsQuery('user-1', '660e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toBe([]);
});
