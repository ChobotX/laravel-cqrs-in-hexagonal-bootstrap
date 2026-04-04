<?php

declare(strict_types=1);

use App\Application\Pagination\PaginatedResult;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\Entry;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\Query\ListEntries\ListEntriesQuery;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\Query\ListEntries\ListEntriesHandler;
use App\Domain\Registry\VersionNumber;
use Tests\Helper\FakeEntryRepository;

it('returns paginated entries for a definition', function (): void {
    $entry1 = new Entry(
        new EntryId('770e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        new DefinitionNamespace('crm'),
        new EntryTitle('John Doe'),
        ['name' => 'John Doe'],
    );

    $entry2 = new Entry(
        new EntryId('880e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        new DefinitionNamespace('crm'),
        new EntryTitle('Jane Doe'),
        ['name' => 'Jane Doe'],
    );

    $repo = new FakeEntryRepository([
        '770e8400-e29b-41d4-a716-446655440000' => $entry1,
        '880e8400-e29b-41d4-a716-446655440000' => $entry2,
    ]);
    $handler = new ListEntriesHandler($repo);

    $paginatedResult = $handler->handle(new ListEntriesQuery(
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        page: 1,
        perPage: 15,
    ));

    expect($paginatedResult)->toBeInstanceOf(PaginatedResult::class)
        ->and($paginatedResult->items)->toHaveCount(2)
        ->and($paginatedResult->total)->toBe(2);
});

it('returns empty result when no entries exist', function (): void {
    $repo = new FakeEntryRepository;
    $handler = new ListEntriesHandler($repo);

    $paginatedResult = $handler->handle(new ListEntriesQuery(
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($paginatedResult->items)->toHaveCount(0)
        ->and($paginatedResult->total)->toBe(0);
});
