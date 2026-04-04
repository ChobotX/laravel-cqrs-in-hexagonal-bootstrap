<?php

declare(strict_types=1);

use App\Domain\Team\Command\AddTeamMember\AddTeamMemberCommand;
use App\Domain\Team\Command\AddTeamMember\AddTeamMemberHandler;
use App\Domain\Team\Contract\Event\TeamMemberAdded;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamSlug;
use App\Domain\Team\Exception\TeamMemberAlreadyExistsException;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\TeamName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTeamMemberRepository;
use Tests\Helper\FakeTeamRepository;

function addTeamMemberTeam(): Team
{
    return new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );
}

it('adds a team member and emits event', function (): void {
    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => addTeamMemberTeam()]);
    $teamMemberRepo = new FakeTeamMemberRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new AddTeamMemberHandler($teamMemberRepo, $teamRepo, $eventCollector);

    $handler->handle(new AddTeamMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        teamId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($teamMemberRepo->added)->toHaveCount(1)
        ->and($teamMemberRepo->added[0]['userId'])->toBe('00000000-0000-0000-0000-000000000010')
        ->and($teamMemberRepo->added[0]['teamId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(TeamMemberAdded::class);
});

it('throws when team not found', function (): void {
    $teamRepo = new FakeTeamRepository;
    $teamMemberRepo = new FakeTeamMemberRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new AddTeamMemberHandler($teamMemberRepo, $teamRepo, $eventCollector);

    $handler->handle(new AddTeamMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        teamId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(TeamNotFoundException::class);

it('throws when already a team member', function (): void {
    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => addTeamMemberTeam()]);
    $teamMemberRepo = new FakeTeamMemberRepository([
        '00000000-0000-0000-0000-000000000010' => ['550e8400-e29b-41d4-a716-446655440000'],
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new AddTeamMemberHandler($teamMemberRepo, $teamRepo, $eventCollector);

    $handler->handle(new AddTeamMemberCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        teamId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(TeamMemberAlreadyExistsException::class);
