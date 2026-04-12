<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\ValueObject;

use App\Domain\FeatureFlag\Contract\Enum\FlagType;

/**
 * Contract-level value object for flag definition used across FeatureFlag commands, queries, and events.
 */
final readonly class FlagDefinition
{
    /**
     * @param  list<string>|null  $options  Available options for select type
     */
    public function __construct(
        /** Field `key` for this contract; see module docs for validation rules. */
        public FlagKey $key,
        /** Field `type` for this contract; see module docs for validation rules. */
        public FlagType $type,
        /** Field `default` for this contract; see module docs for validation rules. */
        public string $default,
        /** Field `defaultEnabled` for this contract; see module docs for validation rules. */
        public bool $defaultEnabled,
        /** Field `label` for this contract; see module docs for validation rules. */
        public string $label,
        /** Longer human-readable explanation for admin UI or emails. */
        public string $description,
        /** Field `group` for this contract; see module docs for validation rules. */
        public string $group,
        /** Field `groupLabel` for this contract; see module docs for validation rules. */
        public string $groupLabel,
        /** Array for `options`; see constructor PHPDoc for structural tags when present. */
        public ?array $options = null,
        /** Optional `pattern`; null means not provided or not applicable. */
        public ?string $pattern = null,
    ) {}
}
