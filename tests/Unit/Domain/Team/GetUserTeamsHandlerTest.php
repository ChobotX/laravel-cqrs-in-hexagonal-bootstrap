<?php

declare(strict_types=1);

use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Query\GetUserTeamsQuery;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;
use App\Domain\Team\Handler\Query\GetUserTeamsHandler;
use App\Domain\Team\ValueObject\TeamName;
use Tests\Helper\FakeTeamMemberRepository;
use Tests\Helper\FakeTeamRepository;

it('returns user teams', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $teamMemberRepo = new FakeTeamMemberRepository(
        memberships: ['user-1' => ['550e8400-e29b-41d4-a716-446655440000']],
    );

    $handler = new GetUserTeamsHandler($teamMemberRepo, $teamRepo);

    $result = $handler->handle(new GetUserTeamsQuery('user-1'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Engineering');
});

it('returns empty when user has no teams', function (): void {
    $teamRepo = new FakeTeamRepository;
    $teamMemberRepo = new FakeTeamMemberRepository;

    $handler = new GetUserTeamsHandler($teamMemberRepo, $teamRepo);

    $result = $handler->handle(new GetUserTeamsQuery('user-1'));

    expect($result)->toBe([]);
});
