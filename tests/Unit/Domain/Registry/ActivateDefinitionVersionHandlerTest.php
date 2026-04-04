<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Command\ActivateDefinitionVersionCommand;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersion;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\Event\DefinitionVersionActivated;
use App\Domain\Registry\Contract\VersionStatus;
use App\Domain\Registry\Exception\DefinitionVersionNotFoundException;
use App\Domain\Registry\Handler\Command\ActivateDefinitionVersionHandler;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\VersionNumber;
use Tests\Helper\FakeDefinitionVersionRepository;
use Tests\Helper\FakeEventCollector;

it('activates the version and deactivates others', function (): void {
    $schema = new Schema([new StringField('name', 'Name', true)]);
    $definitionId = new DefinitionId('550e8400-e29b-41d4-a716-446655440000');

    $version = new DefinitionVersion(
        new DefinitionVersionId('660e8400-e29b-41d4-a716-446655440000'),
        $definitionId,
        new VersionNumber(1),
        $schema,
        VersionStatus::Draft,
    );

    $versionRepo = new FakeDefinitionVersionRepository([
        '660e8400-e29b-41d4-a716-446655440000' => $version,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new ActivateDefinitionVersionHandler($versionRepo, $eventCollector);

    $handler->handle(new ActivateDefinitionVersionCommand(id: '660e8400-e29b-41d4-a716-446655440000'));

    expect($versionRepo->deactivatedForDefinitions)->toBe(['550e8400-e29b-41d4-a716-446655440000'])
        ->and($versionRepo->statusUpdates)->toHaveCount(1)
        ->and($versionRepo->statusUpdates[0]['id'])->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($versionRepo->statusUpdates[0]['status'])->toBe(VersionStatus::Active);
});

it('collects a DefinitionVersionActivated event', function (): void {
    $schema = new Schema([new StringField('name', 'Name', true)]);
    $definitionId = new DefinitionId('550e8400-e29b-41d4-a716-446655440000');

    $version = new DefinitionVersion(
        new DefinitionVersionId('660e8400-e29b-41d4-a716-446655440000'),
        $definitionId,
        new VersionNumber(1),
        $schema,
        VersionStatus::Draft,
    );

    $versionRepo = new FakeDefinitionVersionRepository([
        '660e8400-e29b-41d4-a716-446655440000' => $version,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new ActivateDefinitionVersionHandler($versionRepo, $eventCollector);

    $handler->handle(new ActivateDefinitionVersionCommand(id: '660e8400-e29b-41d4-a716-446655440000'));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(DefinitionVersionActivated::class);
    assert($eventCollector->collected[0] instanceof DefinitionVersionActivated);
    expect($eventCollector->collected[0]->versionId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->version)->toBe(1);
});

it('throws when version not found', function (): void {
    $versionRepo = new FakeDefinitionVersionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new ActivateDefinitionVersionHandler($versionRepo, $eventCollector);

    $handler->handle(new ActivateDefinitionVersionCommand(id: '660e8400-e29b-41d4-a716-446655440000'));
})->throws(DefinitionVersionNotFoundException::class, 'Definition version [660e8400-e29b-41d4-a716-446655440000] not found.');
