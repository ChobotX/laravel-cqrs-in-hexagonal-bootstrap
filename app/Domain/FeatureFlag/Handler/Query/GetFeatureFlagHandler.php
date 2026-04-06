<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Exception\FeatureFlagNotFoundException;
use App\Domain\FeatureFlag\Contract\Query\GetFeatureFlagQuery;
use App\Domain\FeatureFlag\Contract\Repository\FeatureFlagOverrideRepository;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;

/** @implements QueryHandler<GetFeatureFlagQuery, ResolvedFlag> */
final readonly class GetFeatureFlagHandler implements QueryHandler
{
    public function __construct(
        private FeatureFlagDefinitionProvider $featureFlagDefinitionProvider,
        private FeatureFlagOverrideRepository $featureFlagOverrideRepository,
    ) {}

    public function handle(Query $query): ResolvedFlag
    {
        $definition = $this->featureFlagDefinitionProvider->get($query->key);

        if (! $definition instanceof FlagDefinition) {
            throw new FeatureFlagNotFoundException($query->key);
        }

        $override = $this->featureFlagOverrideRepository->findByKey(new FlagKey($query->key));

        return new ResolvedFlag(
            definition: $definition,
            value: $override instanceof FeatureFlagOverride ? $override->value : $definition->default,
            enabled: $override instanceof FeatureFlagOverride ? $override->enabled : $definition->defaultEnabled,
            isOverridden: $override instanceof FeatureFlagOverride,
        );
    }
}
