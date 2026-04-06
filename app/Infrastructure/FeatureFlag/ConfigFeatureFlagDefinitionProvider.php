<?php

declare(strict_types=1);

namespace App\Infrastructure\FeatureFlag;

use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;

final readonly class ConfigFeatureFlagDefinitionProvider implements FeatureFlagDefinitionProvider
{
    /** @var list<FlagDefinition> */
    private array $definitions;

    /** @var array<string, FlagDefinition> */
    private array $definitionsByKey;

    /**
     * @param  array<string, array{label: string, flags: array<string, array{type: string, default: bool|string, label: string, description: string, enabled?: bool, options?: list<string>, pattern?: string}>}>  $groups
     */
    public function __construct(array $groups)
    {
        $definitions = [];
        $definitionsByKey = [];

        foreach ($groups as $groupKey => $group) {
            foreach ($group['flags'] as $flagKey => $flag) {
                $definition = $this->buildDefinition($groupKey, $group['label'], $flagKey, $flag);
                $definitions[] = $definition;
                $definitionsByKey[$definition->key->value] = $definition;
            }
        }

        $this->definitions = $definitions;
        $this->definitionsByKey = $definitionsByKey;
    }

    /**
     * @return list<FlagDefinition>
     */
    public function all(): array
    {
        return $this->definitions;
    }

    public function get(string $key): ?FlagDefinition
    {
        return $this->definitionsByKey[$key] ?? null;
    }

    /**
     * @param  array{type: string, default: bool|string, label: string, description: string, enabled?: bool, options?: list<string>, pattern?: string}  $flag
     */
    private function buildDefinition(string $groupKey, string $groupLabel, string $flagKey, array $flag): FlagDefinition
    {
        $isBooleanType = $flag['type'] === FlagType::Boolean->value;
        $default = is_bool($flag['default'])
            ? ($flag['default'] ? '1' : '0')
            : $flag['default'];
        $defaultEnabled = $isBooleanType
            ? (bool) $flag['default']
            : ($flag['enabled'] ?? true);

        return new FlagDefinition(
            key: new FlagKey($groupKey.'.'.$flagKey),
            type: FlagType::from($flag['type']),
            default: $default,
            defaultEnabled: $defaultEnabled,
            label: $flag['label'],
            description: $flag['description'],
            group: $groupKey,
            groupLabel: $groupLabel,
            options: $flag['options'] ?? null,
            pattern: $flag['pattern'] ?? null,
        );
    }
}
