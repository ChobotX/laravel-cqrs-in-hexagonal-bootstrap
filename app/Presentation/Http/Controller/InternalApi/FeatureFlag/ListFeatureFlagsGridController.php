<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\InternalApi\FeatureFlag;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Sorting\SortDirection;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\Query\ListFeatureFlagsQuery;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('feature_flags.management.read')]
final readonly class ListFeatureFlagsGridController
{
    private const string SORT_KEY = 'key';

    private const string SORT_LABEL = 'label';

    private const string SORT_TYPE = 'type';

    private const string SORT_GROUP = 'group';

    private const string FILTER_GROUP = 'group';

    private const array SORTABLE_COLUMNS = [self::SORT_LABEL, self::SORT_KEY, self::SORT_TYPE, self::SORT_GROUP];

    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): JsonResponse
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        /** @var list<ResolvedFlag> $flags */
        $flags = $this->queryBus->dispatch(new ListFeatureFlagsQuery);

        $flags = $this->applyGroupFilter($flags, $paginationRequest->rawFilters());
        $flags = $this->applySearch($flags, $paginationRequest->search());
        $flags = $this->applySort($flags, $paginationRequest);

        $total = count($flags);

        $pagination = $paginationRequest->pagination();
        $flags = array_slice($flags, $pagination->offset(), $pagination->perPage);

        return new JsonResponse([
            'data' => array_map(fn (ResolvedFlag $resolvedFlag): array => [
                self::SORT_KEY => $resolvedFlag->definition->key->value,
                self::SORT_LABEL => $resolvedFlag->definition->label,
                'description' => $resolvedFlag->definition->description,
                self::SORT_TYPE => $resolvedFlag->definition->type->value,
                self::SORT_GROUP => $resolvedFlag->definition->group,
                'group_label' => $resolvedFlag->definition->groupLabel,
                'enabled' => $resolvedFlag->enabled,
                'value' => $resolvedFlag->value,
                'is_overridden' => $resolvedFlag->isOverridden,
                'has_options' => $resolvedFlag->definition->type !== FlagType::Boolean,
                'edit_url' => route('feature-flags.edit', $resolvedFlag->definition->key->value),
                'reset_url' => route('feature-flags.reset', $resolvedFlag->definition->key->value),
            ], $flags),
            'meta' => [
                'current_page' => $pagination->page,
                'per_page' => $pagination->perPage,
                'total' => $total,
                'total_pages' => $total > 0 ? (int) ceil($total / $pagination->perPage) : 1,
            ],
            'permissions' => [
                'can_update' => $this->authorizationChecker->can($currentUserId, 'feature_flags.management.update'),
            ],
        ]);
    }

    /**
     * @param  list<ResolvedFlag>  $flags
     * @param  array<string, string>  $rawFilters
     * @return list<ResolvedFlag>
     */
    private function applyGroupFilter(array $flags, array $rawFilters): array
    {
        $group = $rawFilters[self::FILTER_GROUP] ?? '';

        if ($group === '') {
            return $flags;
        }

        return array_values(array_filter(
            $flags,
            static fn (ResolvedFlag $resolvedFlag): bool => $resolvedFlag->definition->group === $group,
        ));
    }

    /**
     * @param  list<ResolvedFlag>  $flags
     * @return list<ResolvedFlag>
     */
    private function applySearch(array $flags, string $search): array
    {
        if ($search === '') {
            return $flags;
        }

        $term = mb_strtolower($search);

        return array_values(array_filter(
            $flags,
            static fn (ResolvedFlag $resolvedFlag): bool => str_contains(mb_strtolower($resolvedFlag->definition->key->value), $term)
                || str_contains(mb_strtolower($resolvedFlag->definition->label), $term)
                || str_contains(mb_strtolower($resolvedFlag->definition->description), $term),
        ));
    }

    /**
     * @param  list<ResolvedFlag>  $flags
     * @return list<ResolvedFlag>
     */
    private function applySort(array $flags, PaginationRequest $paginationRequest): array
    {
        $requestSorting = $paginationRequest->sorting();
        $column = self::SORT_LABEL;
        $direction = SortDirection::Asc;

        if ($requestSorting instanceof \App\Application\Sorting\Sorting && in_array($requestSorting->column, self::SORTABLE_COLUMNS, true)) {
            $column = $requestSorting->column;
            $direction = $requestSorting->direction;
        }

        usort($flags, function (ResolvedFlag $a, ResolvedFlag $b) use ($column, $direction): int {
            $valueA = $this->sortValue($a, $column);
            $valueB = $this->sortValue($b, $column);

            $result = strnatcasecmp($valueA, $valueB);

            return $direction === SortDirection::Desc ? -$result : $result;
        });

        return $flags;
    }

    private function sortValue(ResolvedFlag $resolvedFlag, string $column): string
    {
        return match ($column) {
            self::SORT_KEY => $resolvedFlag->definition->key->value,
            self::SORT_LABEL => $resolvedFlag->definition->label,
            self::SORT_TYPE => $resolvedFlag->definition->type->value,
            self::SORT_GROUP => $resolvedFlag->definition->groupLabel,
            default => '',
        };
    }
}
