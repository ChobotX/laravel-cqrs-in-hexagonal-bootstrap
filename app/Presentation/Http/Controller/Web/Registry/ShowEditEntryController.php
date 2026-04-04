<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Contract\JsonSchema;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlug\GetDefinitionBySlugQuery;
use App\Domain\Registry\Contract\Query\GetEntryById\GetEntryByIdQuery;
use App\Domain\Registry\Contract\Query\GetSerializedSchema\GetSerializedSchemaQuery;
use Illuminate\View\View;

#[RequiresPermission('registry.entries.update')]
final readonly class ShowEditEntryController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $namespace, string $slug, string $id): View
    {
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        abort_unless($definition !== null, HttpStatus::NOT_FOUND);

        $entry = $this->queryBus->dispatch(new GetEntryByIdQuery($id));

        abort_unless($entry !== null, HttpStatus::NOT_FOUND);

        $jsonSchema = $this->queryBus->dispatch(new GetSerializedSchemaQuery($definition->id->value));

        abort_unless($jsonSchema instanceof JsonSchema, HttpStatus::NOT_FOUND);

        return view('registry.entries.edit', [
            'definition' => $definition,
            'entry' => $entry,
            'schema' => $jsonSchema->encoded,
        ]);
    }
}
