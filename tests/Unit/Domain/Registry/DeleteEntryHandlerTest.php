<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Command\DeleteEntryCommand;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Event\EntryDeleted;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\Exception\EntryNotFoundException;
use App\Domain\Registry\Handler\Command\DeleteEntryHandler;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\EntryTitle;
use App\Domain\Registry\ValueObject\VersionNumber;
use Tests\Helper\FakeEntryRepository;
use Tests\Helper\FakeEventCollector;

it('deletes the entry', function (): void {
    $entry = new Entry(
        new EntryId('770e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        new DefinitionNamespace('crm'),
        new EntryTitle('John Doe'),
        ['name' => 'John Doe'],
        '550e8400-e29b-41d4-a716-446655440001',
    );

    $entryRepo = new FakeEntryRepository(['770e8400-e29b-41d4-a716-446655440000' => $entry]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteEntryHandler($entryRepo, $eventCollector);

    $handler->handle(new DeleteEntryCommand(id: '770e8400-e29b-41d4-a716-446655440000'));

    expect($entryRepo->deleted)->toBe(['770e8400-e29b-41d4-a716-446655440000']);
});

it('collects an EntryDeleted event', function (): void {
    $entry = new Entry(
        new EntryId('770e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        new DefinitionNamespace('crm'),
        new EntryTitle('John Doe'),
        ['name' => 'John Doe'],
        '550e8400-e29b-41d4-a716-446655440001',
    );

    $entryRepo = new FakeEntryRepository(['770e8400-e29b-41d4-a716-446655440000' => $entry]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteEntryHandler($entryRepo, $eventCollector);

    $handler->handle(new DeleteEntryCommand(id: '770e8400-e29b-41d4-a716-446655440000'));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(EntryDeleted::class);
    assert($eventCollector->collected[0] instanceof EntryDeleted);
    expect($eventCollector->collected[0]->entryId)->toBe('770e8400-e29b-41d4-a716-446655440000');
});

it('throws when entry not found', function (): void {
    $entryRepo = new FakeEntryRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteEntryHandler($entryRepo, $eventCollector);

    $handler->handle(new DeleteEntryCommand(id: '770e8400-e29b-41d4-a716-446655440000'));
})->throws(EntryNotFoundException::class, 'Entry [770e8400-e29b-41d4-a716-446655440000] not found.');
