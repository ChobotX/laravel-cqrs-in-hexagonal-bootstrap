<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\ValueObject;

final readonly class ResolvedFlag
{
    public const string ENABLED = '1';

    public function __construct(
        public FlagDefinition $definition,
        public string $value,
        public bool $enabled,
        public bool $isOverridden,
    ) {}

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
