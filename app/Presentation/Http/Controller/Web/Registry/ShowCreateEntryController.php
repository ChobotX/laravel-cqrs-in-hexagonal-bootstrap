<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Contract\JsonSchema;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Domain\Registry\Contract\Query\GetSerializedSchemaQuery;
use Illuminate\View\View;

#[RequiresPermission('registry.entries.create')]
final readonly class ShowCreateEntryController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $namespace, string $slug): View
    {
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        abort_unless($definition !== null, HttpStatus::NOT_FOUND);

        $jsonSchema = $this->queryBus->dispatch(new GetSerializedSchemaQuery($definition->id->value));

        abort_unless($jsonSchema instanceof JsonSchema, HttpStatus::NOT_FOUND);

        return view('registry.entries.create', [
            'definition' => $definition,
            'schema' => $jsonSchema->encoded,
        ]);
    }
}
