<?php

declare(strict_types=1);

use App\Domain\Team\Command\SyncUserTeams\SyncUserTeamsCommand;
use App\Domain\Team\Command\SyncUserTeams\SyncUserTeamsHandler;
use App\Domain\Team\Contract\Event\TeamMemberAdded;
use App\Domain\Team\Contract\Event\TeamMemberRemoved;
use Tests\Helper\FakeAuthorizationChecker;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTeamMemberRepository;

function createSyncTeamsHandler(
    FakeTeamMemberRepository $fakeTeamMemberRepository,
    FakeEventCollector $fakeEventCollector,
    bool $hasPermission = true,
): SyncUserTeamsHandler {
    $authChecker = new FakeAuthorizationChecker(
        $hasPermission ? ['teams.members.update'] : [],
    );

    return new SyncUserTeamsHandler($fakeTeamMemberRepository, $authChecker, $fakeEventCollector);
}

it('adds and removes team memberships', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository([
        'user-1' => ['team-a', 'team-b'],
    ]);
    $eventCollector = new FakeEventCollector;
    $syncUserTeamsHandler = createSyncTeamsHandler($teamMemberRepo, $eventCollector);

    $syncUserTeamsHandler->handle(new SyncUserTeamsCommand(
        userId: 'user-1',
        submittedTeamIds: ['team-b', 'team-c'],
        actingUserId: 'user-1',
    ));

    expect($teamMemberRepo->added)->toHaveCount(1)
        ->and($teamMemberRepo->added[0]['teamId'])->toBe('team-c')
        ->and($teamMemberRepo->removed)->toHaveCount(1)
        ->and($teamMemberRepo->removed[0]['teamId'])->toBe('team-a')
        ->and($eventCollector->collected)->toHaveCount(2)
        ->and($eventCollector->collected[0])->toBeInstanceOf(TeamMemberAdded::class)
        ->and($eventCollector->collected[1])->toBeInstanceOf(TeamMemberRemoved::class);
});

it('does nothing when submitted matches current', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository([
        'user-1' => ['team-a'],
    ]);
    $eventCollector = new FakeEventCollector;
    $syncUserTeamsHandler = createSyncTeamsHandler($teamMemberRepo, $eventCollector);

    $syncUserTeamsHandler->handle(new SyncUserTeamsCommand(
        userId: 'user-1',
        submittedTeamIds: ['team-a'],
        actingUserId: 'user-1',
    ));

    expect($teamMemberRepo->added)->toBeEmpty()
        ->and($teamMemberRepo->removed)->toBeEmpty()
        ->and($eventCollector->collected)->toBeEmpty();
});

it('adds all when user has no current teams', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository;
    $eventCollector = new FakeEventCollector;
    $syncUserTeamsHandler = createSyncTeamsHandler($teamMemberRepo, $eventCollector);

    $syncUserTeamsHandler->handle(new SyncUserTeamsCommand(
        userId: 'user-1',
        submittedTeamIds: ['team-a', 'team-b'],
        actingUserId: 'user-1',
    ));

    expect($teamMemberRepo->added)->toHaveCount(2)
        ->and($eventCollector->collected)->toHaveCount(2);
});

it('removes all when submitted is empty', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository([
        'user-1' => ['team-a', 'team-b'],
    ]);
    $eventCollector = new FakeEventCollector;
    $syncUserTeamsHandler = createSyncTeamsHandler($teamMemberRepo, $eventCollector);

    $syncUserTeamsHandler->handle(new SyncUserTeamsCommand(
        userId: 'user-1',
        submittedTeamIds: [],
        actingUserId: 'user-1',
    ));

    expect($teamMemberRepo->removed)->toHaveCount(2)
        ->and($eventCollector->collected)->toHaveCount(2);
});

it('skips sync when user lacks permission', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository([
        'user-1' => ['team-a'],
    ]);
    $eventCollector = new FakeEventCollector;
    $syncUserTeamsHandler = createSyncTeamsHandler($teamMemberRepo, $eventCollector, hasPermission: false);

    $syncUserTeamsHandler->handle(new SyncUserTeamsCommand(
        userId: 'user-1',
        submittedTeamIds: ['team-b'],
        actingUserId: 'user-1',
    ));

    expect($teamMemberRepo->added)->toBeEmpty()
        ->and($teamMemberRepo->removed)->toBeEmpty()
        ->and($eventCollector->collected)->toBeEmpty();
});

it('skips sync when submitted is null', function (): void {
    $teamMemberRepo = new FakeTeamMemberRepository([
        'user-1' => ['team-a'],
    ]);
    $eventCollector = new FakeEventCollector;
    $syncUserTeamsHandler = createSyncTeamsHandler($teamMemberRepo, $eventCollector);

    $syncUserTeamsHandler->handle(new SyncUserTeamsCommand(
        userId: 'user-1',
        submittedTeamIds: null,
        actingUserId: 'user-1',
    ));

    expect($teamMemberRepo->added)->toBeEmpty()
        ->and($teamMemberRepo->removed)->toBeEmpty()
        ->and($eventCollector->collected)->toBeEmpty();
});
