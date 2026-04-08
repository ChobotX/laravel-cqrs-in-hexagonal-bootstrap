<?php

declare(strict_types=1);

use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Query\GetDefaultGridPresetQuery;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;
use App\Domain\GridPreset\Handler\Query\GetDefaultGridPresetHandler;
use Tests\Helper\FakeGridPresetRepository;
use Tests\Helper\FakeTeamMembershipChecker;

function defaultHandler(FakeGridPresetRepository $fakeGridPresetRepository): GetDefaultGridPresetHandler
{
    return new GetDefaultGridPresetHandler($fakeGridPresetRepository, new FakeTeamMembershipChecker);
}

it('returns default preset', function (): void {
    $userId = '660e8400-e29b-41d4-a716-446655440000';
    $nonDefault = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440001'), $userId, 'users', 'Not Default', '[]', '[]', '', false, 0);
    $default = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440002'), $userId, 'users', 'Default', '[]', '[]', 'test', true, 1);

    $repo = new FakeGridPresetRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $nonDefault,
        '550e8400-e29b-41d4-a716-446655440002' => $default,
    ]);

    $result = defaultHandler($repo)->handle(new GetDefaultGridPresetQuery($userId, 'users'));

    expect($result)->not->toBeNull();
    assert($result instanceof GridPreset);
    expect($result->name)->toBe('Default')
        ->and($result->isDefault)->toBeTrue();
});

it('returns null when no default exists', function (): void {
    $userId = '660e8400-e29b-41d4-a716-446655440000';
    $preset = new GridPreset(new GridPresetId('550e8400-e29b-41d4-a716-446655440001'), $userId, 'users', 'Not Default', '[]', '[]', '', false, 0);

    $repo = new FakeGridPresetRepository(['550e8400-e29b-41d4-a716-446655440001' => $preset]);

    $result = defaultHandler($repo)->handle(new GetDefaultGridPresetQuery($userId, 'users'));

    expect($result)->toBeNull();
});

it('returns null when no presets exist', function (): void {
    $repo = new FakeGridPresetRepository;

    $result = defaultHandler($repo)->handle(new GetDefaultGridPresetQuery('660e8400-e29b-41d4-a716-446655440000', 'users'));

    expect($result)->toBeNull();
});
