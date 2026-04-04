<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Pagination\PaginatedResult;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\Query\ListDefinitionsQuery;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

#[RequiresPermission('registry.definitions.read')]
final readonly class ListDefinitionsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): View|RedirectResponse
    {
        $pagination = $paginationRequest->pagination();
        $namespace = $paginationRequest->string('namespace')->toString();

        /** @var PaginatedResult<Definition> $paginatedResult */
        $paginatedResult = $this->queryBus->dispatch(new ListDefinitionsQuery(
            page: $pagination->page,
            perPage: $pagination->perPage,
            namespace: $namespace !== '' ? $namespace : null,
        ));

        if ($paginatedResult->isPageOutOfBounds()) {
            return redirect(url()->current().'?'.http_build_query([...$paginationRequest->query(), 'page' => 1]));
        }

        return view('registry.definitions.index', [
            'result' => $paginatedResult,
        ]);
    }
}
