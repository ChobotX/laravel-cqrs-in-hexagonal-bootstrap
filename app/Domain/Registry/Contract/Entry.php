<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract;

use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\VersionNumber;

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
