<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Command\DeleteDefinitionCommand;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Event\DefinitionDeleted;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\Exception\DefinitionHasEntriesException;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use App\Domain\Registry\Handler\Command\DeleteDefinitionHandler;
use App\Domain\Registry\ValueObject\DefinitionName;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;
use App\Domain\Registry\ValueObject\EntryTitle;
use App\Domain\Registry\ValueObject\VersionNumber;
use Tests\Helper\FakeDefinitionRepository;
use Tests\Helper\FakeEntryRepository;
use Tests\Helper\FakeEventCollector;

it('deletes the definition', function (): void {
    $existing = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Employee Directory'),
    );

    $defRepo = new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $entryRepo = new FakeEntryRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteDefinitionHandler($defRepo, $entryRepo, $eventCollector);

    $handler->handle(new DeleteDefinitionCommand(id: '550e8400-e29b-41d4-a716-446655440000'));

    expect($defRepo->deleted)->toBe(['550e8400-e29b-41d4-a716-446655440000']);
});

it('collects a DefinitionDeleted event', function (): void {
    $existing = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Employee Directory'),
    );

    $defRepo = new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $entryRepo = new FakeEntryRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteDefinitionHandler($defRepo, $entryRepo, $eventCollector);

    $handler->handle(new DeleteDefinitionCommand(id: '550e8400-e29b-41d4-a716-446655440000'));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(DefinitionDeleted::class);
    assert($eventCollector->collected[0] instanceof DefinitionDeleted);
    expect($eventCollector->collected[0]->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('throws when definition not found', function (): void {
    $defRepo = new FakeDefinitionRepository;
    $entryRepo = new FakeEntryRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteDefinitionHandler($defRepo, $entryRepo, $eventCollector);

    $handler->handle(new DeleteDefinitionCommand(id: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(DefinitionNotFoundException::class, 'Definition [550e8400-e29b-41d4-a716-446655440000] not found.');

it('throws when definition has entries', function (): void {
    $existing = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Employee Directory'),
    );

    $entry = new Entry(
        new EntryId('770e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        new DefinitionNamespace('crm'),
        new EntryTitle('John Doe'),
        ['name' => 'John Doe'],
        '550e8400-e29b-41d4-a716-446655440001',
    );

    $defRepo = new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $entryRepo = new FakeEntryRepository(['770e8400-e29b-41d4-a716-446655440000' => $entry]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteDefinitionHandler($defRepo, $entryRepo, $eventCollector);

    $handler->handle(new DeleteDefinitionCommand(id: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(DefinitionHasEntriesException::class, 'Definition [550e8400-e29b-41d4-a716-446655440000] has entries and cannot be deleted.');
