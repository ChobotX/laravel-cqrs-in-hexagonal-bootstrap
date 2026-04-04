<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Domain\Registry\Contract\Query\ListDefinitionVersionsQuery;
use App\Presentation\Http\Service\VersionViewMapper;
use Illuminate\View\View;

#[RequiresPermission('registry.definitions.read')]
final readonly class ShowDefinitionController
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

        return view('registry.definitions.show', [
            'definition' => $definition,
            'versions' => VersionViewMapper::mapForView($versions, $namespace, $slug),
        ]);
    }
}
