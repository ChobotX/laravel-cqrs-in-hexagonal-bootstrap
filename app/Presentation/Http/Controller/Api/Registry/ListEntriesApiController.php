<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Registry\Contract\Entry;
use App\Domain\Registry\Query\ListEntriesByDefinitionSlug\ListEntriesByDefinitionSlugQuery;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesApiController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $namespace, string $slug): JsonResponse
    {
        /** @var list<Entry> $entries */
        $entries = $this->queryBus->dispatch(new ListEntriesByDefinitionSlugQuery($namespace, $slug));

        return new JsonResponse(['data' => array_map(
            fn (Entry $entry): array => ['id' => $entry->id->value, 'title' => $entry->title->value],
            $entries,
        )]);
    }
}
