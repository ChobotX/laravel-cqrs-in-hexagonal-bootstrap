<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;

final class FakeFeatureFlagDefinitionProvider implements FeatureFlagDefinitionProvider
{
    /** @var array<string, FlagDefinition> */
    private array $definitionsByKey = [];

    /** @param  list<FlagDefinition>  $definitions */
    public function __construct(
        private readonly array $definitions = [],
    ) {
        foreach ($definitions as $definition) {
            $this->definitionsByKey[$definition->key->value] = $definition;
        }
    }

    /** @return list<FlagDefinition> */
    public function all(): array
    {
        return $this->definitions;
    }

    public function get(string $key): ?FlagDefinition
    {
        return $this->definitionsByKey[$key] ?? null;
    }
}
