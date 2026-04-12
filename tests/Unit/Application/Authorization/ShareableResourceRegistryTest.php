<?php

declare(strict_types=1);

use App\Application\Authorization\ShareableResourceRegistry;

it('supports a registered resource type', function (): void {
    $registry = new ShareableResourceRegistry(['entry' => 'registry.entries']);

    expect($registry->supports('entry'))->toBeTrue();
});

it('does not support an unregistered resource type', function (): void {
    $registry = new ShareableResourceRegistry(['entry' => 'registry.entries']);

    expect($registry->supports('unknown'))->toBeFalse();
});

it('returns correct read permission for known type', function (): void {
    $registry = new ShareableResourceRegistry(['entry' => 'registry.entries']);

    expect($registry->readPermission('entry'))->toBe('registry.entries.read');
});

it('returns correct update permission for known type', function (): void {
    $registry = new ShareableResourceRegistry(['entry' => 'registry.entries']);

    expect($registry->updatePermission('entry'))->toBe('registry.entries.update');
});

it('returns empty-prefix read permission for unknown type', function (): void {
    $registry = new ShareableResourceRegistry(['entry' => 'registry.entries']);

    expect($registry->readPermission('unknown'))->toBe('.read');
});

it('returns empty-prefix update permission for unknown type', function (): void {
    $registry = new ShareableResourceRegistry(['entry' => 'registry.entries']);

    expect($registry->updatePermission('unknown'))->toBe('.update');
});

it('supports multiple registered resource types', function (): void {
    $registry = new ShareableResourceRegistry([
        'entry' => 'registry.entries',
        'document' => 'docs.documents',
    ]);

    expect($registry->supports('entry'))->toBeTrue()
        ->and($registry->supports('document'))->toBeTrue()
        ->and($registry->supports('other'))->toBeFalse();
});

it('returns correct permissions for each registered type', function (): void {
    $registry = new ShareableResourceRegistry([
        'entry' => 'registry.entries',
        'document' => 'docs.documents',
    ]);

    expect($registry->readPermission('document'))->toBe('docs.documents.read')
        ->and($registry->updatePermission('document'))->toBe('docs.documents.update');
});
