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
    public function toDomain(EntryModel $entryModel): Entry
    {
        return new Entry(
            id: new EntryId($entryModel->id),
            definitionId: new DefinitionId($entryModel->definition_id),
            definitionVersion: new VersionNumber($entryModel->definition_version),
            namespace: new DefinitionNamespace($entryModel->namespace),
            title: new EntryTitle($entryModel->title),
            data: $entryModel->data,
        );
    }
}
