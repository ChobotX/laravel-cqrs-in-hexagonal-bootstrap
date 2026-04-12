<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Entity;

use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\EntryTitle;
use App\Domain\Registry\ValueObject\VersionNumber;

/**
 * Immutable read-model snapshot of a Entry returned from queries in the Registry context.
 */
final readonly class Entry
{
    /** @param array<string, mixed> $data */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public EntryId $id,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public DefinitionId $definitionId,
        /** Monotonic or semantic version number for the resource. */
        public VersionNumber $definitionVersion,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public DefinitionNamespace $namespace,
        /** Human-visible label or title. */
        public EntryTitle $title,
        /** Array for `data`; see constructor PHPDoc for structural tags when present. */
        public array $data,
    ) {}
}
