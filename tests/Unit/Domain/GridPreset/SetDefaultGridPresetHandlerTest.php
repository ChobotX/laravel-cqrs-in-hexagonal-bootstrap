<?php

declare(strict_types=1);

use App\Domain\GridPreset\Contract\Command\SetDefaultGridPresetCommand;
use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;
use App\Domain\GridPreset\Exception\GridPresetNotFoundException;
use App\Domain\GridPreset\Exception\GridPresetOwnershipException;
use App\Domain\GridPreset\Handler\Command\SetDefaultGridPresetHandler;
use Tests\Helper\FakeGridPresetRepository;

function defaultPresetFixture(string $id, string $userId, bool $isDefault = false, string $name = 'View'): GridPreset
{
    return new GridPreset(
        new GridPresetId($id),
        $userId,
        'users',
        $name,
        '[]',
        '[]',
        '',
        $isDefault,
        0,
    );
}

it('sets preset as default and clears others', function (): void {
    $userId = '660e8400-e29b-41d4-a716-446655440000';
    $gridPreset = defaultPresetFixture('550e8400-e29b-41d4-a716-446655440001', $userId, true, 'Old Default');
    $preset2 = defaultPresetFixture('550e8400-e29b-41d4-a716-446655440002', $userId, false, 'New Default');

    $repo = new FakeGridPresetRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $gridPreset,
        '550e8400-e29b-41d4-a716-446655440002' => $preset2,
    ]);
    $handler = new SetDefaultGridPresetHandler($repo);

    $handler->handle(new SetDefaultGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440002',
        userId: $userId,
        gridName: 'users',
    ));

    $oldDefault = $repo->findById(new GridPresetId('550e8400-e29b-41d4-a716-446655440001'));
    $newDefault = $repo->findById(new GridPresetId('550e8400-e29b-41d4-a716-446655440002'));

    expect($oldDefault)->not->toBeNull();
    expect($newDefault)->not->toBeNull();
    assert($oldDefault instanceof GridPreset);
    assert($newDefault instanceof GridPreset);
    expect($oldDefault->isDefault)->toBeFalse()
        ->and($newDefault->isDefault)->toBeTrue();
});

it('throws when preset not found', function (): void {
    $repo = new FakeGridPresetRepository;
    $handler = new SetDefaultGridPresetHandler($repo);

    $handler->handle(new SetDefaultGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
        gridName: 'users',
    ));
})->throws(GridPresetNotFoundException::class);

it('throws when user does not own preset', function (): void {
    $gridPreset = defaultPresetFixture('550e8400-e29b-41d4-a716-446655440000', '660e8400-e29b-41d4-a716-446655440000');
    $repo = new FakeGridPresetRepository(['550e8400-e29b-41d4-a716-446655440000' => $gridPreset]);
    $handler = new SetDefaultGridPresetHandler($repo);

    $handler->handle(new SetDefaultGridPresetCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        userId: '770e8400-e29b-41d4-a716-446655440000',
        gridName: 'users',
    ));
})->throws(GridPresetOwnershipException::class);
