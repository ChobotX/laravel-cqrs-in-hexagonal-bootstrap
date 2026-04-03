<?php

declare(strict_types=1);

namespace App\Domain\Registry;

use App\Domain\Registry\Contract\DefinitionId;

final readonly class Definition
{
    public function __construct(
        public DefinitionId $id,
        public DefinitionNamespace $namespace,
        public DefinitionSlug $slug,
        public DefinitionName $name,
    ) {}
}
