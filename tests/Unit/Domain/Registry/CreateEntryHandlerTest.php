<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Command\CreateEntryCommand;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersion;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\Event\EntryCreated;
use App\Domain\Registry\Contract\VersionStatus;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use App\Domain\Registry\Exception\EntryValidationException;
use App\Domain\Registry\Exception\InvalidReferenceException;
use App\Domain\Registry\Exception\NoActiveVersionException;
use App\Domain\Registry\Handler\Command\CreateEntryHandler;
use App\Domain\Registry\ReferenceValidator;
use App\Domain\Registry\Schema\ReferenceField;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\VersionNumber;
use Tests\Helper\FakeDefinitionRepository;
use Tests\Helper\FakeDefinitionVersionRepository;
use Tests\Helper\FakeEntryRepository;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeJsonSchemaValidator;
use Tests\Helper\FakeSchemaSerializer;

/**
 * @param  array{schema?: Schema, defRepo?: FakeDefinitionRepository, versionRepo?: FakeDefinitionVersionRepository, entryRepo?: FakeEntryRepository, validator?: FakeJsonSchemaValidator, serializer?: FakeSchemaSerializer}  $overrides
 * @return array{CreateEntryHandler, FakeEntryRepository, FakeEventCollector}
 */
function createEntryHandlerFixtures(array $overrides = []): array
{
    $definition = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Employee Directory'),
    );

    $schema = $overrides['schema'] ?? new Schema([new StringField('name', 'Name', true)]);

    $activeVersion = new DefinitionVersion(
        new DefinitionVersionId('660e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        $schema,
        VersionStatus::Active,
    );

    $defRepo = $overrides['defRepo'] ?? new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $definition]);
    $versionRepo = $overrides['versionRepo'] ?? new FakeDefinitionVersionRepository(['660e8400-e29b-41d4-a716-446655440000' => $activeVersion]);
    $entryRepo = $overrides['entryRepo'] ?? new FakeEntryRepository;
    $validator = $overrides['validator'] ?? new FakeJsonSchemaValidator;
    $serializer = $overrides['serializer'] ?? new FakeSchemaSerializer;
    $eventCollector = new FakeEventCollector;

    $referenceValidator = new ReferenceValidator($entryRepo);
    $handler = new CreateEntryHandler($defRepo, $versionRepo, $entryRepo, $validator, $serializer, $eventCollector, $referenceValidator);

    return [$handler, $entryRepo, $eventCollector];
}

it('creates an entry via the repository', function (): void {
    [$handler, $entryRepo, $eventCollector] = createEntryHandlerFixtures();

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'John Doe',
        data: ['name' => 'John Doe'],
    ));

    expect($entryRepo->saved)->toHaveCount(1)
        ->and($entryRepo->saved[0]->id->value)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($entryRepo->saved[0]->definitionId->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($entryRepo->saved[0]->definitionVersion->value)->toBe(1)
        ->and($entryRepo->saved[0]->namespace->value)->toBe('crm')
        ->and($entryRepo->saved[0]->title->value)->toBe('John Doe')
        ->and($entryRepo->saved[0]->data)->toBe(['name' => 'John Doe']);
});

it('collects an EntryCreated event', function (): void {
    [$handler, $entryRepo, $eventCollector] = createEntryHandlerFixtures();

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'John Doe',
        data: ['name' => 'John Doe'],
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(EntryCreated::class);
    assert($eventCollector->collected[0] instanceof EntryCreated);
    expect($eventCollector->collected[0]->entryId)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->definitionVersion)->toBe(1)
        ->and($eventCollector->collected[0]->namespace)->toBe('crm')
        ->and($eventCollector->collected[0]->title)->toBe('John Doe');
});

it('throws when definition not found', function (): void {
    [$handler] = createEntryHandlerFixtures(['defRepo' => new FakeDefinitionRepository]);

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'John Doe',
        data: ['name' => 'John Doe'],
    ));
})->throws(DefinitionNotFoundException::class);

it('throws when no active version', function (): void {
    [$handler] = createEntryHandlerFixtures(['versionRepo' => new FakeDefinitionVersionRepository]);

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'John Doe',
        data: ['name' => 'John Doe'],
    ));
})->throws(NoActiveVersionException::class);

it('throws on validation error', function (): void {
    [$handler] = createEntryHandlerFixtures(['validator' => new FakeJsonSchemaValidator(['name is required'])]);

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'John Doe',
        data: [],
    ));
})->throws(EntryValidationException::class);

it('throws on invalid reference', function (): void {
    $schema = new Schema([
        new ReferenceField('author', 'Author', false, 'crm', 'employees'),
    ]);

    [$handler] = createEntryHandlerFixtures(['schema' => $schema]);

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'John Doe',
        data: ['author' => '880e8400-e29b-41d4-a716-446655440000'],
    ));
})->throws(InvalidReferenceException::class);

it('skips reference validation for empty value', function (): void {
    $schema = new Schema([
        new ReferenceField('author', 'Author', false, 'crm', 'employees'),
    ]);

    [$handler, $entryRepo] = createEntryHandlerFixtures(['schema' => $schema]);

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440001',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'Empty Ref',
        data: ['author' => ''],
    ));

    expect($entryRepo->saved)->toHaveCount(1);
});

it('validates references inside a repeater field', function (): void {
    $schema = new Schema([
        new App\Domain\Registry\Schema\RepeaterField('items', 'Items', false, [
            new ReferenceField('ref', 'Ref', false, 'crm', 'employees'),
        ]),
    ]);

    [$handler] = createEntryHandlerFixtures(['schema' => $schema]);

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440002',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'Repeater Ref',
        data: ['items' => [['ref' => '880e8400-e29b-41d4-a716-446655440000']]],
    ));
})->throws(InvalidReferenceException::class);

it('validates references inside an object field', function (): void {
    $schema = new Schema([
        new App\Domain\Registry\Schema\ObjectField('details', 'Details', false, [
            new ReferenceField('ref', 'Ref', false, 'crm', 'employees'),
        ]),
    ]);

    [$handler] = createEntryHandlerFixtures(['schema' => $schema]);

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440003',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'Object Ref',
        data: ['details' => ['ref' => '880e8400-e29b-41d4-a716-446655440000']],
    ));
})->throws(InvalidReferenceException::class);

it('skips repeater validation when items is not an array', function (): void {
    $schema = new Schema([
        new App\Domain\Registry\Schema\RepeaterField('items', 'Items', false, [
            new StringField('x', 'X'),
        ]),
    ]);

    [$handler, $entryRepo] = createEntryHandlerFixtures(['schema' => $schema]);

    $handler->handle(new CreateEntryCommand(
        id: '770e8400-e29b-41d4-a716-446655440004',
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'Non-array repeater',
        data: ['items' => 'not-an-array'],
    ));

    expect($entryRepo->saved)->toHaveCount(1);
});
