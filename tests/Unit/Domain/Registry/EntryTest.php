<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\Entry;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\VersionNumber;

it('can be constructed with value objects and data array', function (): void {
    $data = ['name' => 'John Doe', 'email' => 'john@example.com'];

    $entry = new Entry(
        id: new EntryId('550e8400-e29b-41d4-a716-446655440000'),
        definitionId: new DefinitionId('660e8400-e29b-41d4-a716-446655440000'),
        definitionVersion: new VersionNumber(1),
        namespace: new DefinitionNamespace('crm'),
        title: new EntryTitle('John Doe'),
        data: $data,
    );

    expect($entry->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($entry->definitionId->value)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($entry->definitionVersion->value)->toBe(1)
        ->and($entry->namespace->value)->toBe('crm')
        ->and($entry->title->value)->toBe('John Doe')
        ->and($entry->data)->toBe($data);
});
