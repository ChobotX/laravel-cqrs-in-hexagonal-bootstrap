<?php

declare(strict_types=1);

namespace App\Application\Authorization;

/**
 * Maps a record-share resource_type string to the permission prefix used to
 * authorize read/update on records of that type. Each shareable entity type
 * must be registered here so generic share endpoints can enforce permissions.
 */
final readonly class ShareableResourceRegistry
{
    /** @param array<string, string> $permissionPrefixes resourceType => permission prefix (e.g. 'entry' => 'registry.entries') */
    public function __construct(
        private array $permissionPrefixes,
    ) {}

    public function readPermission(string $resourceType): string
    {
        return $this->permissionPrefix($resourceType).'.read';
    }

    public function updatePermission(string $resourceType): string
    {
        return $this->permissionPrefix($resourceType).'.update';
    }

    public function supports(string $resourceType): bool
    {
        return isset($this->permissionPrefixes[$resourceType]);
    }

    private function permissionPrefix(string $resourceType): string
    {
        return $this->permissionPrefixes[$resourceType] ?? '';
    }
}
