<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\FeatureFlag\Contract\Query\GetAllFeatureFlagValuesQuery;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagChecker;

/** @implements QueryHandler<GetAllFeatureFlagValuesQuery, array<string, array{enabled: bool, value: string}>> */
final readonly class GetAllFeatureFlagValuesHandler implements QueryHandler
{
    public function __construct(
        private FeatureFlagChecker $featureFlagChecker,
    ) {}

    /**
     * @return array<string, array{enabled: bool, value: string}>
     */
    public function handle(Query $query): array
    {
        return $this->featureFlagChecker->all();
    }
}
