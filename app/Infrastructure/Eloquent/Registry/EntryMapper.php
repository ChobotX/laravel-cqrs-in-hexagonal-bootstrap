<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\Entry;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\VersionNumber;

final readonly class EntryMapper
{
    public function toDomain(EntryModel $model): Entry
    {
        return new Entry(
            id: new EntryId($model->id),
            definitionId: new DefinitionId($model->definition_id),
            definitionVersion: new VersionNumber($model->definition_version),
            namespace: new DefinitionNamespace($model->namespace),
            title: new EntryTitle($model->title),
            data: $model->data,
        );
    }
}
