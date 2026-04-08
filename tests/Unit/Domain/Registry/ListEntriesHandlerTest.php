<?php

declare(strict_types=1);

use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;
use App\Application\Pagination\PaginatedResult;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Query\ListEntriesQuery;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\Handler\Query\ListEntriesHandler;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\EntryTitle;
use App\Domain\Registry\ValueObject\VersionNumber;
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

it('filters entries with search filter by title', function (): void {
    $entries = [
        '770e8400-e29b-41d4-a716-446655440000' => new Entry(
            new EntryId('770e8400-e29b-41d4-a716-446655440000'),
            new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
            new VersionNumber(1),
            new DefinitionNamespace('crm'),
            new EntryTitle('Toyota'),
            ['country' => 'Japan'],
        ),
        '880e8400-e29b-41d4-a716-446655440000' => new Entry(
            new EntryId('880e8400-e29b-41d4-a716-446655440000'),
            new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
            new VersionNumber(1),
            new DefinitionNamespace('crm'),
            new EntryTitle('BMW'),
            ['country' => 'Germany'],
        ),
        '990e8400-e29b-41d4-a716-446655440000' => new Entry(
            new EntryId('990e8400-e29b-41d4-a716-446655440000'),
            new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
            new VersionNumber(1),
            new DefinitionNamespace('crm'),
            new EntryTitle('Honda'),
            ['country' => 'Japan'],
        ),
    ];

    $handler = new ListEntriesHandler(new FakeEntryRepository($entries));

    $paginatedResult = $handler->handle(
        new ListEntriesQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000')
            ->withFilters([new Filter('', FilterOperator::Search, 'toyota')]),
    );

    expect($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->items[0]->title->value)->toBe('Toyota');
});

it('returns all entries with empty filters', function (): void {
    $entries = [
        '770e8400-e29b-41d4-a716-446655440000' => new Entry(
            new EntryId('770e8400-e29b-41d4-a716-446655440000'),
            new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
            new VersionNumber(1),
            new DefinitionNamespace('crm'),
            new EntryTitle('Toyota'),
            ['country' => 'Japan'],
        ),
        '880e8400-e29b-41d4-a716-446655440000' => new Entry(
            new EntryId('880e8400-e29b-41d4-a716-446655440000'),
            new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
            new VersionNumber(1),
            new DefinitionNamespace('crm'),
            new EntryTitle('BMW'),
            ['country' => 'Germany'],
        ),
    ];

    $handler = new ListEntriesHandler(new FakeEntryRepository($entries));

    $paginatedResult = $handler->handle(
        new ListEntriesQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000')
            ->withFilters([]),
    );

    expect($paginatedResult->items)->toHaveCount(2);
});

it('search filter returns empty for no match', function (): void {
    $entries = [
        '770e8400-e29b-41d4-a716-446655440000' => new Entry(
            new EntryId('770e8400-e29b-41d4-a716-446655440000'),
            new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
            new VersionNumber(1),
            new DefinitionNamespace('crm'),
            new EntryTitle('Toyota'),
            ['country' => 'Japan'],
        ),
    ];

    $handler = new ListEntriesHandler(new FakeEntryRepository($entries));

    $paginatedResult = $handler->handle(
        new ListEntriesQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000')
            ->withFilters([new Filter('', FilterOperator::Search, 'nonexistent')]),
    );

    expect($paginatedResult->items)->toBeEmpty()
        ->and($paginatedResult->total)->toBe(0);
});

it('empty search term returns all entries', function (): void {
    $entries = [
        '770e8400-e29b-41d4-a716-446655440000' => new Entry(
            new EntryId('770e8400-e29b-41d4-a716-446655440000'),
            new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
            new VersionNumber(1),
            new DefinitionNamespace('crm'),
            new EntryTitle('Toyota'),
            ['country' => 'Japan'],
        ),
        '880e8400-e29b-41d4-a716-446655440000' => new Entry(
            new EntryId('880e8400-e29b-41d4-a716-446655440000'),
            new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
            new VersionNumber(1),
            new DefinitionNamespace('crm'),
            new EntryTitle('BMW'),
            ['country' => 'Germany'],
        ),
    ];

    $handler = new ListEntriesHandler(new FakeEntryRepository($entries));

    $paginatedResult = $handler->handle(
        new ListEntriesQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000')
            ->withFilters([new Filter('', FilterOperator::Search, '')]),
    );

    expect($paginatedResult->items)->toHaveCount(2);
});

it('supports withFilters immutable copy', function (): void {
    $query = new ListEntriesQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000');
    $listEntriesQuery = $query->withFilters([new Filter('', FilterOperator::Search, 'test')]);

    expect($query->filters())->toBe([])
        ->and($listEntriesQuery->filters())->toHaveCount(1)
        ->and($listEntriesQuery->filters()[0]->operator)->toBe(FilterOperator::Search)
        ->and($listEntriesQuery->filters()[0]->value)->toBe('test');
});

it('combines search filter with pagination', function (): void {
    $entries = [];
    for ($i = 1; $i <= 10; $i++) {
        $id = sprintf('550e8400-e29b-41d4-a716-44665544%04d', $i);
        $entries[$id] = new Entry(
            new EntryId($id),
            new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
            new VersionNumber(1),
            new DefinitionNamespace('crm'),
            new EntryTitle('Entry '.$i),
            ['value' => $i],
        );
    }

    $handler = new ListEntriesHandler(new FakeEntryRepository($entries));

    $paginatedResult = $handler->handle(
        new ListEntriesQuery(
            definitionId: '550e8400-e29b-41d4-a716-446655440000',
            page: 1,
            perPage: 5,
        )->withFilters([new Filter('', FilterOperator::Search, 'entry')]),
    );

    expect($paginatedResult->items)->toHaveCount(5)
        ->and($paginatedResult->total)->toBe(10);
});
