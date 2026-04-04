<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Command\CreateDefinitionVersionCommand;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\Event\DefinitionVersionCreated;
use App\Domain\Registry\Contract\VersionStatus;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use App\Domain\Registry\Handler\Command\CreateDefinitionVersionHandler;
use Tests\Helper\FakeDefinitionRepository;
use Tests\Helper\FakeDefinitionVersionRepository;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeSchemaSerializer;

it('creates a definition version', function (): void {
    $definition = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Employee Directory'),
    );

    $defRepo = new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $definition]);
    $versionRepo = new FakeDefinitionVersionRepository;
    $serializer = new FakeSchemaSerializer;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateDefinitionVersionHandler($defRepo, $versionRepo, $serializer, $eventCollector);

    $handler->handle(new CreateDefinitionVersionCommand(
        id: '660e8400-e29b-41d4-a716-446655440000',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        schemaData: ['type' => 'object'],
    ));

    expect($versionRepo->saved)->toHaveCount(1)
        ->and($versionRepo->saved[0]->id->value)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($versionRepo->saved[0]->definitionId->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($versionRepo->saved[0]->version->value)->toBe(1)
        ->and($versionRepo->saved[0]->status)->toBe(VersionStatus::Draft);
});

it('collects a DefinitionVersionCreated event', function (): void {
    $definition = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Employee Directory'),
    );

    $defRepo = new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $definition]);
    $versionRepo = new FakeDefinitionVersionRepository;
    $serializer = new FakeSchemaSerializer;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateDefinitionVersionHandler($defRepo, $versionRepo, $serializer, $eventCollector);

    $handler->handle(new CreateDefinitionVersionCommand(
        id: '660e8400-e29b-41d4-a716-446655440000',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        schemaData: ['type' => 'object'],
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(DefinitionVersionCreated::class);
    assert($eventCollector->collected[0] instanceof DefinitionVersionCreated);
    expect($eventCollector->collected[0]->versionId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->version)->toBe(1);
});

it('throws when definition not found', function (): void {
    $defRepo = new FakeDefinitionRepository;
    $versionRepo = new FakeDefinitionVersionRepository;
    $serializer = new FakeSchemaSerializer;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateDefinitionVersionHandler($defRepo, $versionRepo, $serializer, $eventCollector);

    $handler->handle(new CreateDefinitionVersionCommand(
        id: '660e8400-e29b-41d4-a716-446655440000',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        schemaData: ['type' => 'object'],
    ));
})->throws(DefinitionNotFoundException::class, 'Definition [550e8400-e29b-41d4-a716-446655440000] not found.');
