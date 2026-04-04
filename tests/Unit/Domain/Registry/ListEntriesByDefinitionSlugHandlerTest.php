<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\Entry;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\Query\ListEntriesByDefinitionSlug\ListEntriesByDefinitionSlugHandler;
use App\Domain\Registry\Query\ListEntriesByDefinitionSlug\ListEntriesByDefinitionSlugQuery;
use App\Domain\Registry\VersionNumber;
use Tests\Helper\FakeEntryRepository;

it('returns entries for a definition slug', function (): void {
    $entry = new Entry(
        new EntryId('770e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        new DefinitionNamespace('crm'),
        new EntryTitle('John Doe'),
        ['name' => 'John Doe'],
    );

    $repo = new FakeEntryRepository(['770e8400-e29b-41d4-a716-446655440000' => $entry]);
    $handler = new ListEntriesByDefinitionSlugHandler($repo);

    $result = $handler->handle(new ListEntriesByDefinitionSlugQuery(namespace: 'crm', slug: 'employees'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->id->value)->toBe('770e8400-e29b-41d4-a716-446655440000');
});

it('returns empty list when no entries match', function (): void {
    $repo = new FakeEntryRepository;
    $handler = new ListEntriesByDefinitionSlugHandler($repo);

    $result = $handler->handle(new ListEntriesByDefinitionSlugQuery(namespace: 'crm', slug: 'employees'));

    expect($result)->toHaveCount(0);
});
