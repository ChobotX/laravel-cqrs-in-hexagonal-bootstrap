<?php

declare(strict_types=1);

use App\Domain\GridPreset\Contract\Command\SaveGridPresetCommand;
use App\Domain\GridPreset\Contract\Enum\PresetScope;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;
use App\Domain\GridPreset\Exception\GridPresetScopePermissionException;
use App\Domain\GridPreset\Handler\Command\SaveGridPresetHandler;
use Tests\Helper\FakeAuthorizationChecker;
use Tests\Helper\FakeGridPresetRepository;
use Tests\Helper\FakeTeamMembershipChecker;

function saveHandler(
    FakeGridPresetRepository $fakeGridPresetRepository,
    ?FakeAuthorizationChecker $fakeAuthorizationChecker = null,
    ?FakeTeamMembershipChecker $fakeTeamMembershipChecker = null,
): SaveGridPresetHandler {
    return new SaveGridPresetHandler(
        $fakeGridPresetRepository,
        $fakeAuthorizationChecker ?? new FakeAuthorizationChecker,
        $fakeTeamMembershipChecker ?? new FakeTeamMembershipChecker,
    );
}

function saveCommand(
    string $scope = 'personal',
    ?string $teamId = null,
    string $userId = '660e8400-e29b-41d4-a716-446655440000',
): SaveGridPresetCommand {
    return new SaveGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: $userId,
        gridName: 'users',
        name: 'My View',
        filters: '[]',
        sorting: '[]',
        search: '',
        isDefault: false,
        position: 0,
        scope: $scope,
        teamId: $teamId,
    );
}

it('saves a new grid preset', function (): void {
    $repo = new FakeGridPresetRepository;

    saveHandler($repo)->handle(new SaveGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
        gridName: 'users',
        name: 'My View',
        filters: '[]',
        sorting: '[]',
        search: '',
        isDefault: false,
        position: 0,
    ));

    expect($repo->saved)->toHaveCount(1)
        ->and($repo->saved[0]->name)->toBe('My View')
        ->and($repo->saved[0]->gridName)->toBe('users')
        ->and($repo->saved[0]->userId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($repo->saved[0]->isDefault)->toBeFalse();
});

it('saves preset with filters and sorting', function (): void {
    $repo = new FakeGridPresetRepository;

    saveHandler($repo)->handle(new SaveGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
        gridName: 'teams',
        name: 'Active Teams',
        filters: '[{"column":"status","operator":"eq","value":"active"}]',
        sorting: '[{"column":"name","direction":"asc"}]',
        search: 'engineering',
        isDefault: true,
        position: 1,
    ));

    expect($repo->saved[0]->filters)->toBe('[{"column":"status","operator":"eq","value":"active"}]')
        ->and($repo->saved[0]->sorting)->toBe('[{"column":"name","direction":"asc"}]')
        ->and($repo->saved[0]->search)->toBe('engineering')
        ->and($repo->saved[0]->isDefault)->toBeTrue()
        ->and($repo->saved[0]->position)->toBe(1);
});

it('saves preset with team scope when user has team permission', function (): void {
    $userId = '660e8400-e29b-41d4-a716-446655440000';
    $teamId = '880e8400-e29b-41d4-a716-446655440000';
    $repo = new FakeGridPresetRepository;

    saveHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update'], ['teams.management.update' => 'team']),
        new FakeTeamMembershipChecker([$userId => [$teamId]]),
    )->handle(saveCommand(scope: 'team', teamId: $teamId, userId: $userId));

    expect($repo->saved[0]->scope)->toBe(PresetScope::Team)
        ->and($repo->saved[0]->teamId)->toBe($teamId);
});

it('saves preset with global scope when user has all-scope permission', function (): void {
    $repo = new FakeGridPresetRepository;

    saveHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update']),
    )->handle(saveCommand(scope: 'global'));

    expect($repo->saved[0]->scope)->toBe(PresetScope::Global)
        ->and($repo->saved[0]->teamId)->toBeNull();
});

it('allows all-scope manager to save team preset in any team', function (): void {
    $teamId = '880e8400-e29b-41d4-a716-446655440000';
    $repo = new FakeGridPresetRepository;

    saveHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update']),
    )->handle(saveCommand(scope: 'team', teamId: $teamId));

    expect($repo->saved[0]->scope)->toBe(PresetScope::Team)
        ->and($repo->saved[0]->teamId)->toBe($teamId);
});

it('throws when user lacks permission for global scope', function (): void {
    $repo = new FakeGridPresetRepository;

    saveHandler($repo)->handle(saveCommand(scope: 'global'));
})->throws(GridPresetScopePermissionException::class);

it('throws when user has team-scope permission for global scope', function (): void {
    $repo = new FakeGridPresetRepository;

    saveHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update'], ['teams.management.update' => 'team']),
    )->handle(saveCommand(scope: 'global'));
})->throws(GridPresetScopePermissionException::class);

it('throws when user lacks permission for team scope', function (): void {
    $repo = new FakeGridPresetRepository;

    saveHandler($repo)->handle(saveCommand(scope: 'team', teamId: '880e8400-e29b-41d4-a716-446655440000'));
})->throws(GridPresetScopePermissionException::class);

it('throws when team scope has null team id', function (): void {
    $repo = new FakeGridPresetRepository;

    saveHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update'], ['teams.management.update' => 'team']),
    )->handle(saveCommand(scope: 'team'));
})->throws(GridPresetScopePermissionException::class);

it('throws when team manager saves preset for another team', function (): void {
    $userId = '660e8400-e29b-41d4-a716-446655440000';
    $ownTeamId = '880e8400-e29b-41d4-a716-446655440000';
    $otherTeamId = '990e8400-e29b-41d4-a716-446655440000';
    $repo = new FakeGridPresetRepository;

    saveHandler(
        $repo,
        new FakeAuthorizationChecker(['teams.management.update'], ['teams.management.update' => 'team']),
        new FakeTeamMembershipChecker([$userId => [$ownTeamId]]),
    )->handle(saveCommand(scope: 'team', teamId: $otherTeamId, userId: $userId));
})->throws(GridPresetScopePermissionException::class);

it('overwrites existing preset with same id', function (): void {
    $repo = new FakeGridPresetRepository;
    $saveGridPresetHandler = saveHandler($repo);

    $saveGridPresetHandler->handle(new SaveGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
        gridName: 'users',
        name: 'Original',
        filters: '[]',
        sorting: '[]',
        search: '',
        isDefault: false,
        position: 0,
    ));

    $saveGridPresetHandler->handle(new SaveGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
        gridName: 'users',
        name: 'Updated',
        filters: '[]',
        sorting: '[]',
        search: '',
        isDefault: false,
        position: 0,
    ));

    $preset = $repo->findById(new GridPresetId('550e8400-e29b-41d4-a716-446655440000'));

    expect($preset)->not->toBeNull();
    assert($preset instanceof App\Domain\GridPreset\Contract\Entity\GridPreset);
    expect($preset->name)->toBe('Updated');
});
