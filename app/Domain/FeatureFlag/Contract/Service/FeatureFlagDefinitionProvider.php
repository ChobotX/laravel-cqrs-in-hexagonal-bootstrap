<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Service;

use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;

interface FeatureFlagDefinitionProvider
{
    /**
     * @return list<FlagDefinition>
     */
    public function all(): array;

    public function get(string $key): ?FlagDefinition;
}
