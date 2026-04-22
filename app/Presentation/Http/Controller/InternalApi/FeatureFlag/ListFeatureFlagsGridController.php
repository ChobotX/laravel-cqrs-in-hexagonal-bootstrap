<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\InternalApi\FeatureFlag;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\QueryBus;
use App\Domain\FeatureFlag\Contract\Query\ListFeatureFlagsGridQuery;
use App\Domain\FeatureFlag\Contract\ValueObject\FeatureFlagGridRow;
use App\Domain\FeatureFlag\Contract\ValueObject\FeatureFlagsGridResult;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('feature_flags.management.read')]
final readonly class ListFeatureFlagsGridController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): JsonResponse
    {
        /** @var FeatureFlagsGridResult $result */
        $result = $this->queryBus->dispatch(new ListFeatureFlagsGridQuery(
            pagination: $paginationRequest->pagination(),
            sorting: $paginationRequest->sorting(),
            search: $paginationRequest->search(),
            groupFilter: $paginationRequest->rawFilters()['group'] ?? '',
            actingUserId: $this->authenticatedUser->id() ?? '',
        ));

        return new JsonResponse([
            'data' => array_map(fn (FeatureFlagGridRow $row): array => [
                'key' => $row->key,
                'label' => $row->label,
                'description' => $row->description,
                'type' => $row->type,
                'group' => $row->group,
                'group_label' => $row->groupLabel,
                'enabled' => $row->enabled,
                'value' => $row->value,
                'is_overridden' => $row->isOverridden,
                'has_options' => $row->hasOptions,
                'edit_url' => route('feature-flags.edit', $row->key),
                'reset_url' => route('feature-flags.reset', $row->key),
            ], $result->rows),
            'meta' => [
                'current_page' => $result->page,
                'per_page' => $result->perPage,
                'total' => $result->total,
                'total_pages' => $result->totalPages,
            ],
            'permissions' => [
                'can_update' => $result->permissions->canUpdate,
            ],
        ]);
    }
}
