<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Query\GetDefinitionBySlug\GetDefinitionBySlugQuery;
use App\Domain\Registry\Query\ListEntries\ListEntriesQuery;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\View\View;

#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest, string $namespace, string $slug): View
    {
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        abort_unless($definition !== null, HttpStatus::NOT_FOUND);

        $pagination = $paginationRequest->pagination();
        $paginatedResult = $this->queryBus->dispatch(new ListEntriesQuery(
            definitionId: $definition->id->value,
            page: $pagination->page,
            perPage: $pagination->perPage,
        ));

        return view('registry.entries.index', [
            'result' => $paginatedResult,
            'definition' => $definition,
        ]);
    }
}
