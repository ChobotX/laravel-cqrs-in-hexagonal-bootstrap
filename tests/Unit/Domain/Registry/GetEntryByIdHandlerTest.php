<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\Entry;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\Query\GetEntryByIdQuery;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\Handler\Query\GetEntryByIdHandler;
use App\Domain\Registry\VersionNumber;
use Tests\Helper\FakeEntryRepository;

it('returns an entry when found', function (): void {
    $entry = new Entry(
        new EntryId('770e8400-e29b-41d4-a716-446655440000'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new VersionNumber(1),
        new DefinitionNamespace('crm'),
        new EntryTitle('John Doe'),
        ['name' => 'John Doe'],
    );

    $repo = new FakeEntryRepository(['770e8400-e29b-41d4-a716-446655440000' => $entry]);
    $handler = new GetEntryByIdHandler($repo);

    $result = $handler->handle(new GetEntryByIdQuery(id: '770e8400-e29b-41d4-a716-446655440000'));

    assert($result instanceof Entry);
    expect($result->id->value)->toBe('770e8400-e29b-41d4-a716-446655440000');
});

it('returns null when not found', function (): void {
    $repo = new FakeEntryRepository;
    $handler = new GetEntryByIdHandler($repo);

    $result = $handler->handle(new GetEntryByIdQuery(id: '770e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toBeNull();
});
