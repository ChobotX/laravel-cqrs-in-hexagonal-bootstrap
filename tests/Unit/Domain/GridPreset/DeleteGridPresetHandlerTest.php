<?php

declare(strict_types=1);

use App\Domain\GridPreset\Contract\Command\DeleteGridPresetCommand;
use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Enum\PresetScope;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;
use App\Domain\GridPreset\Exception\GridPresetNotFoundException;
use App\Domain\GridPreset\Exception\GridPresetOwnershipException;
use App\Domain\GridPreset\Handler\Command\DeleteGridPresetHandler;
use Tests\Helper\FakeAuthorizationChecker;
use Tests\Helper\FakeGridPresetRepository;
use Tests\Helper\FakeTeamMembershipChecker;

function presetFixture(string $id, string $userId, string $gridName = 'users', string $name = 'My View'): GridPreset
{
    return new GridPreset(
        new GridPresetId($id),
        $userId,
        $gridName,
        $name,
        '[]',
        '[]',
        '',
        false,
        0,
    );
}

function sharedPresetFixture(string $id, string $userId, PresetScope $presetScope, ?string $teamId = null): GridPreset
{
    return new GridPreset(
        new GridPresetId($id),
        $userId,
        'users',
        'Shared View',
        '[]',
        '[]',
        '',
        false,
        0,
        $presetScope,
        $teamId,
    );
}

function deleteHandler(
    FakeGridPresetRepository $fakeGridPresetRepository,
    ?FakeAuthorizationChecker $fakeAuthorizationChecker = null,
    ?FakeTeamMembershipChecker $fakeTeamMembershipChecker = null,
): DeleteGridPresetHandler {
    return new DeleteGridPresetHandler(
        $fakeGridPresetRepository,
        $fakeAuthorizationChecker ?? new FakeAuthorizationChecker,
        $fakeTeamMembershipChecker ?? new FakeTeamMembershipChecker,
    );
}

it('deletes own preset', function (): void {
    $gridPreset = presetFixture('550e8400-e29b-41d4-a716-446655440000', '660e8400-e29b-41d4-a716-446655440000');
    $repo = new FakeGridPresetRepository(['550e8400-e29b-41d4-a716-446655440000' => $gridPreset]);

    deleteHandler($repo)->handle(new DeleteGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
    ));

    expect($repo->deleted)->toBe(['550e8400-e29b-41d4-a716-446655440000'])
        ->and($repo->findById(new GridPresetId('550e8400-e29b-41d4-a716-446655440000')))->toBeNull();
});

it('throws when preset not found', function (): void {
    $repo = new FakeGridPresetRepository;

    deleteHandler($repo)->handle(new DeleteGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(GridPresetNotFoundException::class);

it('throws when user does not own preset', function (): void {
    $gridPreset = presetFixture('550e8400-e29b-41d4-a716-446655440000', '660e8400-e29b-41d4-a716-446655440000');
    $repo = new FakeGridPresetRepository(['550e8400-e29b-41d4-a716-446655440000' => $gridPreset]);

    deleteHandler($repo)->handle(new DeleteGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: '770e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(GridPresetOwnershipException::class);

it('allows team manager to delete team preset in their team', function (): void {
    $ownerId = '660e8400-e29b-41d4-a716-446655440000';
    $managerId = '770e8400-e29b-41d4-a716-446655440000';
    $teamId = '880e8400-e29b-41d4-a716-446655440000';
    $presetId = '550e8400-e29b-41d4-a716-446655440000';

    $gridPreset = sharedPresetFixture($presetId, $ownerId, PresetScope::Team, $teamId);
    $repo = new FakeGridPresetRepository([$presetId => $gridPreset]);

    deleteHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update'], ['teams.management.update' => 'team']),
        new FakeTeamMembershipChecker([$managerId => [$teamId]]),
    )->handle(new DeleteGridPresetCommand(id: $presetId, userId: $managerId));

    expect($repo->deleted)->toBe([$presetId]);
});

it('throws when team manager deletes preset from another team', function (): void {
    $ownerId = '660e8400-e29b-41d4-a716-446655440000';
    $managerId = '770e8400-e29b-41d4-a716-446655440000';
    $presetTeamId = '880e8400-e29b-41d4-a716-446655440000';
    $managerTeamId = '990e8400-e29b-41d4-a716-446655440000';
    $presetId = '550e8400-e29b-41d4-a716-446655440000';

    $gridPreset = sharedPresetFixture($presetId, $ownerId, PresetScope::Team, $presetTeamId);
    $repo = new FakeGridPresetRepository([$presetId => $gridPreset]);

    deleteHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update'], ['teams.management.update' => 'team']),
        new FakeTeamMembershipChecker([$managerId => [$managerTeamId]]),
    )->handle(new DeleteGridPresetCommand(id: $presetId, userId: $managerId));
})->throws(GridPresetOwnershipException::class);

it('allows global manager to delete global preset', function (): void {
    $ownerId = '660e8400-e29b-41d4-a716-446655440000';
    $managerId = '770e8400-e29b-41d4-a716-446655440000';
    $presetId = '550e8400-e29b-41d4-a716-446655440000';

    $gridPreset = sharedPresetFixture($presetId, $ownerId, PresetScope::Global);
    $repo = new FakeGridPresetRepository([$presetId => $gridPreset]);

    deleteHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update']),
    )->handle(new DeleteGridPresetCommand(id: $presetId, userId: $managerId));

    expect($repo->deleted)->toBe([$presetId]);
});

it('throws when non-manager deletes global preset', function (): void {
    $ownerId = '660e8400-e29b-41d4-a716-446655440000';
    $userId = '770e8400-e29b-41d4-a716-446655440000';
    $presetId = '550e8400-e29b-41d4-a716-446655440000';

    $gridPreset = sharedPresetFixture($presetId, $ownerId, PresetScope::Global);
    $repo = new FakeGridPresetRepository([$presetId => $gridPreset]);

    deleteHandler($repo)->handle(new DeleteGridPresetCommand(id: $presetId, userId: $userId));
})->throws(GridPresetOwnershipException::class);

it('allows owner to delete their own team preset', function (): void {
    $ownerId = '660e8400-e29b-41d4-a716-446655440000';
    $teamId = '880e8400-e29b-41d4-a716-446655440000';
    $presetId = '550e8400-e29b-41d4-a716-446655440000';

    $gridPreset = sharedPresetFixture($presetId, $ownerId, PresetScope::Team, $teamId);
    $repo = new FakeGridPresetRepository([$presetId => $gridPreset]);

    deleteHandler($repo)->handle(new DeleteGridPresetCommand(id: $presetId, userId: $ownerId));

    expect($repo->deleted)->toBe([$presetId]);
});

it('throws when team preset has null team id', function (): void {
    $ownerId = '660e8400-e29b-41d4-a716-446655440000';
    $managerId = '770e8400-e29b-41d4-a716-446655440000';
    $presetId = '550e8400-e29b-41d4-a716-446655440000';

    $gridPreset = sharedPresetFixture($presetId, $ownerId, PresetScope::Team);
    $repo = new FakeGridPresetRepository([$presetId => $gridPreset]);

    deleteHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update'], ['teams.management.update' => 'team']),
    )->handle(new DeleteGridPresetCommand(id: $presetId, userId: $managerId));
})->throws(GridPresetOwnershipException::class);

it('throws when user has no management permission for team preset', function (): void {
    $ownerId = '660e8400-e29b-41d4-a716-446655440000';
    $userId = '770e8400-e29b-41d4-a716-446655440000';
    $teamId = '880e8400-e29b-41d4-a716-446655440000';
    $presetId = '550e8400-e29b-41d4-a716-446655440000';

    $gridPreset = sharedPresetFixture($presetId, $ownerId, PresetScope::Team, $teamId);
    $repo = new FakeGridPresetRepository([$presetId => $gridPreset]);

    deleteHandler(
        $repo,
        new FakeAuthorizationChecker,
        new FakeTeamMembershipChecker([$userId => [$teamId]]),
    )->handle(new DeleteGridPresetCommand(id: $presetId, userId: $userId));
})->throws(GridPresetOwnershipException::class);

it('allows all-scope manager to delete team preset from any team', function (): void {
    $ownerId = '660e8400-e29b-41d4-a716-446655440000';
    $managerId = '770e8400-e29b-41d4-a716-446655440000';
    $teamId = '880e8400-e29b-41d4-a716-446655440000';
    $presetId = '550e8400-e29b-41d4-a716-446655440000';

    $gridPreset = sharedPresetFixture($presetId, $ownerId, PresetScope::Team, $teamId);
    $repo = new FakeGridPresetRepository([$presetId => $gridPreset]);

    deleteHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update']),
    )->handle(new DeleteGridPresetCommand(id: $presetId, userId: $managerId));

    expect($repo->deleted)->toBe([$presetId]);
});
