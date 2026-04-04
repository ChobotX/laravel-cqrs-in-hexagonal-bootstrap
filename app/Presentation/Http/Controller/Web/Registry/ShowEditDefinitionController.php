<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Definition;
use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\Query\GetDefinitionBySlug\GetDefinitionBySlugQuery;
use App\Domain\Registry\Query\ListDefinitionVersions\ListDefinitionVersionsQuery;
use Illuminate\View\View;

#[RequiresPermission('registry.definitions.update')]
final readonly class ShowEditDefinitionController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $namespace, string $slug): View
    {
        /** @var Definition|null $definition */
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        if ($definition === null) {
            abort(HttpStatus::NOT_FOUND);
        }

        /** @var list<DefinitionVersion> $versions */
        $versions = $this->queryBus->dispatch(new ListDefinitionVersionsQuery($definition->id->value));

        return view('registry.definitions.edit', [
            'definition' => $definition,
            'versions' => $versions,
        ]);
    }
}
