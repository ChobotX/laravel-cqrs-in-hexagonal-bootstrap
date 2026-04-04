<?php

declare(strict_types=1);

use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Query\GetTeamsForUsersQuery;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;
use App\Domain\Team\Handler\Query\GetTeamsForUsersHandler;
use App\Domain\Team\ValueObject\TeamName;
use Tests\Helper\FakeTeamMemberRepository;
use Tests\Helper\FakeTeamRepository;

it('returns teams grouped by user id', function (): void {
    $teamA = new Team(new TeamId('00000000-0000-0000-0000-00000000000a'), new TeamName('Alpha'), new TeamSlug('alpha'), '', null);
    $teamB = new Team(new TeamId('00000000-0000-0000-0000-00000000000b'), new TeamName('Beta'), new TeamSlug('beta'), '', null);

    $teamMemberRepo = new FakeTeamMemberRepository([
        'user-1' => ['00000000-0000-0000-0000-00000000000a', '00000000-0000-0000-0000-00000000000b'],
        'user-2' => ['00000000-0000-0000-0000-00000000000a'],
    ]);

    $teamRepo = new FakeTeamRepository([
        '00000000-0000-0000-0000-00000000000a' => $teamA,
        '00000000-0000-0000-0000-00000000000b' => $teamB,
    ]);

    $handler = new GetTeamsForUsersHandler($teamMemberRepo, $teamRepo);

    $result = $handler->handle(new GetTeamsForUsersQuery(['user-1', 'user-2']));

    expect($result)->toHaveCount(2)
        ->and($result['user-1'])->toHaveCount(2)
        ->and($result['user-2'])->toHaveCount(1)
        ->and($result['user-2'][0]->name->value)->toBe('Alpha');
});

it('returns empty lists when users have no teams', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository;
    $teamRepo = new FakeTeamRepository;

    $handler = new GetTeamsForUsersHandler($teamMemberRepo, $teamRepo);

    $result = $handler->handle(new GetTeamsForUsersQuery(['user-1']));

    expect($result)->toHaveCount(1)
        ->and($result['user-1'])->toBeEmpty();
});

it('returns empty map for empty input', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository;
    $teamRepo = new FakeTeamRepository;

    $handler = new GetTeamsForUsersHandler($teamMemberRepo, $teamRepo);

    $result = $handler->handle(new GetTeamsForUsersQuery([]));

    expect($result)->toBeEmpty();
});
