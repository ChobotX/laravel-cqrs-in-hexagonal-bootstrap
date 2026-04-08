<?php

declare(strict_types=1);

use App\Domain\GridPreset\Contract\Query\GetPresetShareCapabilitiesQuery;
use App\Domain\GridPreset\Handler\Query\GetPresetShareCapabilitiesHandler;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;
use App\Domain\Team\ValueObject\TeamName;
use Tests\Helper\FakeAuthorizationChecker;
use Tests\Helper\FakeTeamMemberRepository;
use Tests\Helper\FakeTeamRepository;

function shareCapabilitiesHandler(
    ?FakeAuthorizationChecker $fakeAuthorizationChecker = null,
    ?FakeTeamMemberRepository $fakeTeamMemberRepository = null,
    ?FakeTeamRepository $fakeTeamRepository = null,
): GetPresetShareCapabilitiesHandler {
    return new GetPresetShareCapabilitiesHandler(
        $fakeAuthorizationChecker ?? new FakeAuthorizationChecker,
        $fakeTeamMemberRepository ?? new FakeTeamMemberRepository,
        $fakeTeamRepository ?? new FakeTeamRepository,
    );
}

it('returns no share capabilities when user has no management permission', function (): void {
    $presetShareCapabilities = shareCapabilitiesHandler()->handle(new GetPresetShareCapabilitiesQuery('user-1'));

    expect($presetShareCapabilities->canShareTeam)->toBeFalse()
        ->and($presetShareCapabilities->canShareGlobal)->toBeFalse()
        ->and($presetShareCapabilities->shareableTeams)->toBe([]);
});

it('returns global and team share when user has all-scope permission', function (): void {
    $teamId = '880e8400-e29b-41d4-a716-446655440000';
    $team = new Team(new TeamId($teamId), new TeamName('Engineering'), new TeamSlug('engineering'), '', null);

    $presetShareCapabilities = shareCapabilitiesHandler(
        new FakeAuthorizationChecker(['teams.management.update']),
        new FakeTeamMemberRepository(['user-1' => [$teamId]]),
        new FakeTeamRepository([$teamId => $team]),
    )->handle(new GetPresetShareCapabilitiesQuery('user-1'));

    expect($presetShareCapabilities->canShareTeam)->toBeTrue()
        ->and($presetShareCapabilities->canShareGlobal)->toBeTrue()
        ->and($presetShareCapabilities->shareableTeams)->toBe([['id' => $teamId, 'name' => 'Engineering']]);
});

it('returns team share only when user has team-scope permission', function (): void {
    $teamId = '880e8400-e29b-41d4-a716-446655440000';
    $team = new Team(new TeamId($teamId), new TeamName('Design'), new TeamSlug('design'), '', null);

    $presetShareCapabilities = shareCapabilitiesHandler(
        new FakeAuthorizationChecker(['teams.management.update'], ['teams.management.update' => 'team']),
        new FakeTeamMemberRepository(['user-1' => [$teamId]]),
        new FakeTeamRepository([$teamId => $team]),
    )->handle(new GetPresetShareCapabilitiesQuery('user-1'));

    expect($presetShareCapabilities->canShareTeam)->toBeTrue()
        ->and($presetShareCapabilities->canShareGlobal)->toBeFalse()
        ->and($presetShareCapabilities->shareableTeams)->toBe([['id' => $teamId, 'name' => 'Design']]);
});

it('returns empty teams when user has team permission but no team memberships', function (): void {
    $presetShareCapabilities = shareCapabilitiesHandler(
        new FakeAuthorizationChecker(['teams.management.update'], ['teams.management.update' => 'team']),
    )->handle(new GetPresetShareCapabilitiesQuery('user-1'));

    expect($presetShareCapabilities->canShareTeam)->toBeTrue()
        ->and($presetShareCapabilities->canShareGlobal)->toBeFalse()
        ->and($presetShareCapabilities->shareableTeams)->toBe([]);
});

it('returns multiple teams when user belongs to several', function (): void {
    $teamIdA = '880e8400-e29b-41d4-a716-446655440001';
    $teamIdB = '880e8400-e29b-41d4-a716-446655440002';
    $teamA = new Team(new TeamId($teamIdA), new TeamName('Alpha'), new TeamSlug('alpha'), '', null);
    $teamB = new Team(new TeamId($teamIdB), new TeamName('Beta'), new TeamSlug('beta'), '', null);

    $presetShareCapabilities = shareCapabilitiesHandler(
        new FakeAuthorizationChecker(['teams.management.update']),
        new FakeTeamMemberRepository(['user-1' => [$teamIdA, $teamIdB]]),
        new FakeTeamRepository([$teamIdA => $teamA, $teamIdB => $teamB]),
    )->handle(new GetPresetShareCapabilitiesQuery('user-1'));

    expect($presetShareCapabilities->shareableTeams)->toHaveCount(2)
        ->and($presetShareCapabilities->shareableTeams[0]['name'])->toBe('Alpha')
        ->and($presetShareCapabilities->shareableTeams[1]['name'])->toBe('Beta');
});
