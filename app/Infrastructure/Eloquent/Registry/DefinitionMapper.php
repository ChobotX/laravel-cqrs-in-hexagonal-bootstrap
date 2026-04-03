<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Definition;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;

final readonly class DefinitionMapper
{
    public function toDomain(DefinitionModel $model): Definition
    {
        return new Definition(
            id: new DefinitionId($model->id),
            namespace: new DefinitionNamespace($model->namespace),
            slug: new DefinitionSlug($model->slug),
            name: new DefinitionName($model->name),
        );
    }
}
