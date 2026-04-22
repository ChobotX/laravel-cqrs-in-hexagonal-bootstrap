<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\Registry;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Query\ListDefinitionsQuery;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('registry.definitions.read')]
final readonly class ListDefinitionsApiController
{
    private const int MAX_DEFINITIONS = 1000;

    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): JsonResponse
    {
        $paginatedResult = $this->queryBus->dispatch(new ListDefinitionsQuery(page: 1, perPage: self::MAX_DEFINITIONS));

        return new JsonResponse(['data' => array_map(
            fn (Definition $definition): array => [
                'namespace' => $definition->namespace->value,
                'slug' => $definition->slug->value,
                'name' => $definition->name->value,
            ],
            $paginatedResult->items,
        )]);
    }
}
