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
        /** @var FeatureFlagsGridResult $featureFlagsGridResult */
        $featureFlagsGridResult = $this->queryBus->dispatch(new ListFeatureFlagsGridQuery(
            pagination: $paginationRequest->pagination(),
            sorting: $paginationRequest->sorting(),
            search: $paginationRequest->search(),
            groupFilter: $paginationRequest->rawFilters()['group'] ?? '',
            actingUserId: $this->authenticatedUser->id() ?? '',
        ));

        return new JsonResponse([
            'data' => array_map(fn (FeatureFlagGridRow $featureFlagGridRow): array => [
                'key' => $featureFlagGridRow->key,
                'label' => $featureFlagGridRow->label,
                'description' => $featureFlagGridRow->description,
                'type' => $featureFlagGridRow->type,
                'group' => $featureFlagGridRow->group,
                'group_label' => $featureFlagGridRow->groupLabel,
                'enabled' => $featureFlagGridRow->enabled,
                'value' => $featureFlagGridRow->value,
                'is_overridden' => $featureFlagGridRow->isOverridden,
                'has_options' => $featureFlagGridRow->hasOptions,
                'edit_url' => route('feature-flags.edit', $featureFlagGridRow->key),
                'reset_url' => route('feature-flags.reset', $featureFlagGridRow->key),
            ], $featureFlagsGridResult->rows),
            'meta' => [
                'current_page' => $featureFlagsGridResult->page,
                'per_page' => $featureFlagsGridResult->perPage,
                'total' => $featureFlagsGridResult->total,
                'total_pages' => $featureFlagsGridResult->totalPages,
            ],
            'permissions' => [
                'can_update' => $featureFlagsGridResult->permissions->canUpdate,
            ],
        ]);
    }
}
