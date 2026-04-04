<?php

declare(strict_types=1);

use App\Domain\Registry\Command\DeprecateDefinitionVersion\DeprecateDefinitionVersionHandler;
use App\Domain\Registry\Contract\Command\DeprecateDefinitionVersion\DeprecateDefinitionVersionCommand;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersion;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\Event\DefinitionVersionDeprecated;
use App\Domain\Registry\Contract\VersionStatus;
use App\Domain\Registry\Exception\DefinitionVersionNotFoundException;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\VersionNumber;
use Tests\Helper\FakeDefinitionVersionRepository;
use Tests\Helper\FakeEventCollector;

it('deprecates the version', function (): void {
    $schema = new Schema([new StringField('name', 'Name', true)]);

    $version = new DefinitionVersion(
        new DefinitionVersionId('660e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        $schema,
        VersionStatus::Active,
    );

    $versionRepo = new FakeDefinitionVersionRepository([
        '660e8400-e29b-41d4-a716-446655440000' => $version,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeprecateDefinitionVersionHandler($versionRepo, $eventCollector);

    $handler->handle(new DeprecateDefinitionVersionCommand(id: '660e8400-e29b-41d4-a716-446655440000'));

    expect($versionRepo->statusUpdates)->toHaveCount(1)
        ->and($versionRepo->statusUpdates[0]['id'])->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($versionRepo->statusUpdates[0]['status'])->toBe(VersionStatus::Deprecated);
});

it('collects a DefinitionVersionDeprecated event', function (): void {
    $schema = new Schema([new StringField('name', 'Name', true)]);

    $version = new DefinitionVersion(
        new DefinitionVersionId('660e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        $schema,
        VersionStatus::Active,
    );

    $versionRepo = new FakeDefinitionVersionRepository([
        '660e8400-e29b-41d4-a716-446655440000' => $version,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeprecateDefinitionVersionHandler($versionRepo, $eventCollector);

    $handler->handle(new DeprecateDefinitionVersionCommand(id: '660e8400-e29b-41d4-a716-446655440000'));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(DefinitionVersionDeprecated::class);
    assert($eventCollector->collected[0] instanceof DefinitionVersionDeprecated);
    expect($eventCollector->collected[0]->versionId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->version)->toBe(1);
});

it('throws when version not found', function (): void {
    $versionRepo = new FakeDefinitionVersionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeprecateDefinitionVersionHandler($versionRepo, $eventCollector);

    $handler->handle(new DeprecateDefinitionVersionCommand(id: '660e8400-e29b-41d4-a716-446655440000'));
})->throws(DefinitionVersionNotFoundException::class, 'Definition version [660e8400-e29b-41d4-a716-446655440000] not found.');
