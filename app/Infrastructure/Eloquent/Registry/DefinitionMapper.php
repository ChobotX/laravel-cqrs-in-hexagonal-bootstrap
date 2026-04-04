<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\ValueObject\DefinitionName;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;

final readonly class DefinitionMapper
{
    public function toDomain(DefinitionModel $definitionModel): Definition
    {
        return new Definition(
            id: new DefinitionId($definitionModel->id),
            namespace: new DefinitionNamespace($definitionModel->namespace),
            slug: new DefinitionSlug($definitionModel->slug),
            name: new DefinitionName($definitionModel->name),
        );
    }
}
