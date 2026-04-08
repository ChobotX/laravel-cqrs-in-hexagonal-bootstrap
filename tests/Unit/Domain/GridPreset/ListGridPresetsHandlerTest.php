<?php

declare(strict_types=1);

use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Enum\PresetScope;
use App\Domain\GridPreset\Contract\Query\ListGridPresetsQuery;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;
use App\Domain\GridPreset\Handler\Query\ListGridPresetsHandler;
use Tests\Helper\FakeGridPresetRepository;
use Tests\Helper\FakeTeamMembershipChecker;

function listHandler(FakeGridPresetRepository $fakeGridPresetRepository, ?FakeTeamMembershipChecker $fakeTeamMembershipChecker = null): ListGridPresetsHandler
{
    return new ListGridPresetsHandler($fakeGridPresetRepository, $fakeTeamMembershipChecker ?? new FakeTeamMembershipChecker);
}

it('returns presets for user and grid', function (): void {
    $userId = '660e8400-e29b-41d4-a716-446655440000';
    $preset1 = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440001'), $userId, 'users', 'View A', '[]', '[]', '', false, 0);
    $preset2 = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440002'), $userId, 'users', 'View B', '[]', '[]', '', false, 1);
    $otherGrid = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440003'), $userId, 'teams', 'Team View', '[]', '[]', '', false, 0);

    $repo = new FakeGridPresetRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $preset1,
        '550e8400-e29b-41d4-a716-446655440002' => $preset2,
        '550e8400-e29b-41d4-a716-446655440003' => $otherGrid,
    ]);

    $result = listHandler($repo)->handle(new ListGridPresetsQuery($userId, 'users'));

    expect($result)->toHaveCount(2)
        ->and($result[0]->name)->toBe('View A')
        ->and($result[1]->name)->toBe('View B');
});

it('returns empty for no presets', function (): void {
    $repo = new FakeGridPresetRepository;

    $result = listHandler($repo)->handle(new ListGridPresetsQuery('660e8400-e29b-41d4-a716-446655440000', 'users'));

    expect($result)->toBeEmpty();
});

it('filters by user id', function (): void {
    $user1 = '660e8400-e29b-41d4-a716-446655440001';
    $user2 = '660e8400-e29b-41d4-a716-446655440002';
    $preset1 = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440001'), $user1, 'users', 'User1 View', '[]', '[]', '', false, 0);
    $preset2 = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440002'), $user2, 'users', 'User2 View', '[]', '[]', '', false, 0);

    $repo = new FakeGridPresetRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $preset1,
        '550e8400-e29b-41d4-a716-446655440002' => $preset2,
    ]);

    $result = listHandler($repo)->handle(new ListGridPresetsQuery($user1, 'users'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name)->toBe('User1 View');
});

it('includes global presets for any user', function (): void {
    $user1 = '660e8400-e29b-41d4-a716-446655440001';
    $user2 = '660e8400-e29b-41d4-a716-446655440002';
    $personal = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440001'), $user1, 'users', 'My View', '[]', '[]', '', false, 0);
    $global = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440002'), $user2, 'users', 'Global View', '[]', '[]', '', false, 0, PresetScope::Global);

    $repo = new FakeGridPresetRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $personal,
        '550e8400-e29b-41d4-a716-446655440002' => $global,
    ]);

    $result = listHandler($repo)->handle(new ListGridPresetsQuery($user1, 'users'));

    expect($result)->toHaveCount(2);
});

it('includes team presets when user is team member', function (): void {
    $user1 = '660e8400-e29b-41d4-a716-446655440001';
    $user2 = '660e8400-e29b-41d4-a716-446655440002';
    $teamId = '880e8400-e29b-41d4-a716-446655440001';
    $teamPreset = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440001'), $user2, 'users', 'Team View', '[]', '[]', '', false, 0, PresetScope::Team, $teamId);

    $repo = new FakeGridPresetRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $teamPreset,
    ]);
    $teamChecker = new FakeTeamMembershipChecker([$user1 => [$teamId]]);

    $result = listHandler($repo, $teamChecker)->handle(new ListGridPresetsQuery($user1, 'users'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name)->toBe('Team View');
});

it('excludes team presets when user is not team member', function (): void {
    $user1 = '660e8400-e29b-41d4-a716-446655440001';
    $user2 = '660e8400-e29b-41d4-a716-446655440002';
    $teamId = '880e8400-e29b-41d4-a716-446655440001';
    $teamPreset = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440001'), $user2, 'users', 'Team View', '[]', '[]', '', false, 0, PresetScope::Team, $teamId);

    $repo = new FakeGridPresetRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $teamPreset,
    ]);

    $result = listHandler($repo)->handle(new ListGridPresetsQuery($user1, 'users'));

    expect($result)->toBeEmpty();
});
