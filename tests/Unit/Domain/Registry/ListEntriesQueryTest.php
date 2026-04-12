<?php

declare(strict_types=1);

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\ScopeAwareQuery;
use App\Application\Authorization\ScopeTarget;
use App\Application\Authorization\ShareableScopeQuery;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Registry\Contract\Query\ListEntriesQuery;

it('has default values for page and perPage', function (): void {
    $query = new ListEntriesQuery('550e8400-e29b-41d4-a716-446655440000');

    expect($query->page)->toBe(1)
        ->and($query->perPage)->toBe(ListEntriesQuery::DEFAULT_PER_PAGE);
});

it('returns ScopeTarget::User from scopeTarget', function (): void {
    $query = new ListEntriesQuery('550e8400-e29b-41d4-a716-446655440000');

    expect($query->scopeTarget())->toBe(ScopeTarget::User);
});

it('returns entry from shareableResourceType', function (): void {
    $query = new ListEntriesQuery('550e8400-e29b-41d4-a716-446655440000');

    expect($query->shareableResourceType())->toBe('entry');
});

it('implements ScopeAwareQuery and ShareableScopeQuery', function (): void {
    $query = new ListEntriesQuery('550e8400-e29b-41d4-a716-446655440000');

    expect($query)->toBeInstanceOf(ScopeAwareQuery::class)
        ->and($query)->toBeInstanceOf(ShareableScopeQuery::class);
});

it('returns null accessContext by default', function (): void {
    $query = new ListEntriesQuery('550e8400-e29b-41d4-a716-446655440000');

    expect($query->accessContext())->toBeNull();
});

it('withAccessContext returns new instance with context set', function (): void {
    $query = new ListEntriesQuery('550e8400-e29b-41d4-a716-446655440000');
    $context = new AccessContext(AccessScope::Own, ['user-id-1']);

    $listEntriesQuery = $query->withAccessContext($context);

    expect($listEntriesQuery)->not->toBe($query)
        ->and($listEntriesQuery->accessContext())->toBe($context);
});

it('withAccessContext preserves other fields', function (): void {
    $query = new ListEntriesQuery('550e8400-e29b-41d4-a716-446655440000', 2, 5);
    $context = new AccessContext(AccessScope::All, null);

    $listEntriesQuery = $query->withAccessContext($context);

    expect($listEntriesQuery->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($listEntriesQuery->page)->toBe(2)
        ->and($listEntriesQuery->perPage)->toBe(5);
});

it('original query is unmodified after withAccessContext', function (): void {
    $query = new ListEntriesQuery('550e8400-e29b-41d4-a716-446655440000');
    $context = new AccessContext(AccessScope::Team, ['team-id-1']);

    $query->withAccessContext($context);

    expect($query->accessContext())->toBeNull();
});
