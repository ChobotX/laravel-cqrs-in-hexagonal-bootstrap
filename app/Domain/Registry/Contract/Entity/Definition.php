<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Entity;

use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\ValueObject\DefinitionName;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;

final readonly class Definition
{
    public function __construct(
        public DefinitionId $id,
        public DefinitionNamespace $namespace,
        public DefinitionSlug $slug,
        public DefinitionName $name,
    ) {}
}
