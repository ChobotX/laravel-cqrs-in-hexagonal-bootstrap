<?php

declare(strict_types=1);

namespace App\Domain\Registry;

use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\EntryId;

final readonly class Entry
{
    /** @param array<string, mixed> $data */
    public function __construct(
        public EntryId $id,
        public DefinitionId $definitionId,
        public VersionNumber $definitionVersion,
        public DefinitionNamespace $namespace,
        public EntryTitle $title,
        public array $data,
    ) {}
}
