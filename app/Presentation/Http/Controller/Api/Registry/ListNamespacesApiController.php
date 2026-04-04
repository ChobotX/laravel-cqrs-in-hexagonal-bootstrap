<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Registry\Contract\Query\ListDefinitionNamespaces\ListDefinitionNamespacesQuery;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('registry.definitions.read')]
final readonly class ListNamespacesApiController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): JsonResponse
    {
        /** @var list<string> $namespaces */
        $namespaces = $this->queryBus->dispatch(new ListDefinitionNamespacesQuery);

        return new JsonResponse(['data' => $namespaces]);
    }
}
