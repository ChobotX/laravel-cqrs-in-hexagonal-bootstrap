<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\ValueObject;

/**
 * Contract-level value object for resolved flag used across FeatureFlag commands, queries, and events.
 */
final readonly class ResolvedFlag
{
    public const string ENABLED = '1';

    public function __construct(
        /** Field `definition` for this contract; see module docs for validation rules. */
        public FlagDefinition $definition,
        /** Field `value` for this contract; see module docs for validation rules. */
        public string $value,
        /** Field `enabled` for this contract; see module docs for validation rules. */
        public bool $enabled,
        /** Boolean flag for this state or capability. */
        public bool $isOverridden,
    ) {}

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
