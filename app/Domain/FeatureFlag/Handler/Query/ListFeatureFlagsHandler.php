<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\FeatureFlag\Contract\Query\ListFeatureFlagsQuery;
use App\Domain\FeatureFlag\Contract\Repository\FeatureFlagOverrideRepository;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;

/** @implements QueryHandler<ListFeatureFlagsQuery, list<ResolvedFlag>> */
final readonly class ListFeatureFlagsHandler implements QueryHandler
{
    public function __construct(
        private FeatureFlagDefinitionProvider $featureFlagDefinitionProvider,
        private FeatureFlagOverrideRepository $featureFlagOverrideRepository,
    ) {}

    /**
     * @return list<ResolvedFlag>
     */
    public function handle(Query $query): array
    {
        $definitions = $this->featureFlagDefinitionProvider->all();
        $overrides = $this->featureFlagOverrideRepository->findAll();

        $overrideMap = [];
        foreach ($overrides as $override) {
            $overrideMap[$override->key] = $override;
        }

        $resolved = [];
        foreach ($definitions as $definition) {
            $key = $definition->key->value;
            $override = $overrideMap[$key] ?? null;

            $resolved[] = new ResolvedFlag(
                definition: $definition,
                value: $override->value ?? $definition->default,
                enabled: $override->enabled ?? $definition->defaultEnabled,
                isOverridden: $override !== null,
            );
        }

        return $resolved;
    }
}
