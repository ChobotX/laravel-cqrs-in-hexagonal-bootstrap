<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Entity;

use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\EntryTitle;
use App\Domain\Registry\ValueObject\VersionNumber;

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
