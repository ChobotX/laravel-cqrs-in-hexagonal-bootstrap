<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\ValueObject;

/**
 * Pre-projected team tree node with parentId pruned to visible ancestors and roles resolved.
 */
final readonly class TeamTreeGridNode
{
    /**
     * @param  list<TeamTreeGridMember>  $members
     */
    public function __construct(
        public string $id,
        /** Empty string when parent is outside the visible subtree. */
        public string $parentId,
        public string $name,
        public string $slug,
        public int $memberCount,
        public array $members,
    ) {}
}
