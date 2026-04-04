<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract;

use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;

final readonly class Definition
{
    public function __construct(
        public DefinitionId $id,
        public DefinitionNamespace $namespace,
        public DefinitionSlug $slug,
        public DefinitionName $name,
    ) {}
}
