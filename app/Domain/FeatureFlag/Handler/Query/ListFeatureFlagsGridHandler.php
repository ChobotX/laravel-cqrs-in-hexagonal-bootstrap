<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Handler\Query;

use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Auth\AuthorizationChecker;
use App\Contract\Bus\QueryBus;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\Query\ListFeatureFlagsGridQuery;
use App\Domain\FeatureFlag\Contract\Query\ListFeatureFlagsQuery;
use App\Domain\FeatureFlag\Contract\ValueObject\FeatureFlagGridRow;
use App\Domain\FeatureFlag\Contract\ValueObject\FeatureFlagsGridPermissions;
use App\Domain\FeatureFlag\Contract\ValueObject\FeatureFlagsGridResult;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;

/** @implements QueryHandler<ListFeatureFlagsGridQuery, FeatureFlagsGridResult> */
final readonly class ListFeatureFlagsGridHandler implements QueryHandler
{
    private const string SORT_KEY = 'key';

    private const string SORT_LABEL = 'label';

    private const string SORT_TYPE = 'type';

    private const string SORT_GROUP = 'group';

    private const array SORTABLE_COLUMNS = [self::SORT_LABEL, self::SORT_KEY, self::SORT_TYPE, self::SORT_GROUP];

    public function __construct(
        private QueryBus $queryBus,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function handle(Query $query): FeatureFlagsGridResult
    {
        /** @var list<ResolvedFlag> $flags */
        $flags = $this->queryBus->dispatch(new ListFeatureFlagsQuery);

        $flags = $this->applyGroupFilter($flags, $query->groupFilter);
        $flags = $this->applySearch($flags, $query->search);
        $flags = $this->applySort($flags, $query->sorting);

        $total = count($flags);
        $flags = array_slice($flags, $query->pagination->offset(), $query->pagination->perPage);

        return new FeatureFlagsGridResult(
            rows: array_map(fn (ResolvedFlag $flag): FeatureFlagGridRow => $this->mapRow($flag), $flags),
            total: $total,
            page: $query->pagination->page,
            perPage: $query->pagination->perPage,
            totalPages: $total > 0 ? (int) ceil($total / $query->pagination->perPage) : 1,
            permissions: new FeatureFlagsGridPermissions(
                canUpdate: $this->authorizationChecker->can($query->actingUserId, 'feature_flags.management.update'),
            ),
        );
    }

    /**
     * @param  list<ResolvedFlag>  $flags
     * @return list<ResolvedFlag>
     */
    private function applyGroupFilter(array $flags, string $group): array
    {
        if ($group === '') {
            return $flags;
        }

        return array_values(array_filter(
            $flags,
            static fn (ResolvedFlag $flag): bool => $flag->definition->group === $group,
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
            static fn (ResolvedFlag $flag): bool => str_contains(mb_strtolower($flag->definition->key->value), $term)
                || str_contains(mb_strtolower($flag->definition->label), $term)
                || str_contains(mb_strtolower($flag->definition->description), $term),
        ));
    }

    /**
     * @param  list<ResolvedFlag>  $flags
     * @return list<ResolvedFlag>
     */
    private function applySort(array $flags, ?Sorting $sorting): array
    {
        $column = self::SORT_LABEL;
        $direction = SortDirection::Asc;

        if ($sorting instanceof Sorting && in_array($sorting->column, self::SORTABLE_COLUMNS, true)) {
            $column = $sorting->column;
            $direction = $sorting->direction;
        }

        usort($flags, function (ResolvedFlag $a, ResolvedFlag $b) use ($column, $direction): int {
            $result = strnatcasecmp($this->sortValue($a, $column), $this->sortValue($b, $column));

            return $direction === SortDirection::Desc ? -$result : $result;
        });

        return $flags;
    }

    private function sortValue(ResolvedFlag $flag, string $column): string
    {
        return match ($column) {
            self::SORT_KEY => $flag->definition->key->value,
            self::SORT_LABEL => $flag->definition->label,
            self::SORT_TYPE => $flag->definition->type->value,
            self::SORT_GROUP => $flag->definition->groupLabel,
            default => '',
        };
    }

    private function mapRow(ResolvedFlag $flag): FeatureFlagGridRow
    {
        return new FeatureFlagGridRow(
            key: $flag->definition->key->value,
            label: $flag->definition->label,
            description: $flag->definition->description,
            type: $flag->definition->type->value,
            group: $flag->definition->group,
            groupLabel: $flag->definition->groupLabel,
            enabled: $flag->enabled,
            value: $flag->value,
            isOverridden: $flag->isOverridden,
            hasOptions: $flag->definition->type !== FlagType::Boolean,
        );
    }
}
