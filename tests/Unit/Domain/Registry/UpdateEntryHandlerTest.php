<?php

declare(strict_types=1);

use App\Domain\Registry\Command\UpdateEntry\UpdateEntryCommand;
use App\Domain\Registry\Command\UpdateEntry\UpdateEntryHandler;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\Event\EntryUpdated;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\Entry;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\Exception\EntryNotFoundException;
use App\Domain\Registry\Exception\EntryValidationException;
use App\Domain\Registry\Exception\InvalidReferenceException;
use App\Domain\Registry\Schema\ReferenceField;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\VersionNumber;
use App\Domain\Registry\VersionStatus;
use Tests\Helper\FakeDefinitionVersionRepository;
use Tests\Helper\FakeEntryRepository;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeJsonSchemaValidator;
use Tests\Helper\FakeSchemaSerializer;

/**
 * @param array{schema?: Schema, entryRepo?: FakeEntryRepository, versionRepo?: FakeDefinitionVersionRepository, validator?: FakeJsonSchemaValidator, serializer?: FakeSchemaSerializer} $overrides
 * @return array{UpdateEntryHandler, FakeEntryRepository, FakeEventCollector}
 */
function updateEntryHandlerFixtures(array $overrides = []): array
{
    $schema = $overrides['schema'] ?? new Schema([new StringField('name', 'Name', true)]);

    $entry = new Entry(
        new EntryId('770e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        new DefinitionNamespace('crm'),
        new EntryTitle('Old Title'),
        ['name' => 'Old Name'],
    );

    $version = new DefinitionVersion(
        new DefinitionVersionId('660e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        $schema,
        VersionStatus::Active,
    );

    $entryRepo = $overrides['entryRepo'] ?? new FakeEntryRepository(['770e8400-e29b-41d4-a716-446655440000' => $entry]);
    $versionRepo = $overrides['versionRepo'] ?? new FakeDefinitionVersionRepository(['660e8400-e29b-41d4-a716-446655440000' => $version]);
    $validator = $overrides['validator'] ?? new FakeJsonSchemaValidator;
    $serializer = $overrides['serializer'] ?? new FakeSchemaSerializer;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateEntryHandler($entryRepo, $versionRepo, $validator, $serializer, $eventCollector);

    return [$handler, $entryRepo, $eventCollector];
}

it('updates an entry', function (): void {
    [$handler, $entryRepo, $eventCollector] = updateEntryHandlerFixtures();

    $handler->handle(new UpdateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        title: 'New Title',
        data: ['name' => 'New Name'],
    ));

    expect($entryRepo->saved)->toHaveCount(1)
        ->and($entryRepo->saved[0]->title->value)->toBe('New Title')
        ->and($entryRepo->saved[0]->data)->toBe(['name' => 'New Name']);
});

it('collects an EntryUpdated event', function (): void {
    [$handler, $entryRepo, $eventCollector] = updateEntryHandlerFixtures();

    $handler->handle(new UpdateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        title: 'New Title',
        data: ['name' => 'New Name'],
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(EntryUpdated::class);
    assert($eventCollector->collected[0] instanceof EntryUpdated);
    expect($eventCollector->collected[0]->entryId)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->title)->toBe('New Title');
});

it('throws when entry not found', function (): void {
    [$handler] = updateEntryHandlerFixtures(['entryRepo' => new FakeEntryRepository]);

    $handler->handle(new UpdateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        title: 'New Title',
        data: ['name' => 'New Name'],
    ));
})->throws(EntryNotFoundException::class);

it('throws on validation error', function (): void {
    [$handler] = updateEntryHandlerFixtures(['validator' => new FakeJsonSchemaValidator(['name is required'])]);

    $handler->handle(new UpdateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        title: 'New Title',
        data: [],
    ));
})->throws(EntryValidationException::class);

it('throws on invalid reference', function (): void {
    $schema = new Schema([
        new ReferenceField('author', 'Author', false, 'crm', 'employees'),
    ]);

    [$handler] = updateEntryHandlerFixtures(['schema' => $schema]);

    $handler->handle(new UpdateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        title: 'New Title',
        data: ['author' => '880e8400-e29b-41d4-a716-446655440000'],
    ));
})->throws(InvalidReferenceException::class);
