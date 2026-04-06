<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\ValueObject;

use App\Domain\FeatureFlag\Contract\Enum\FlagType;

final readonly class FlagDefinition
{
    /**
     * @param  list<string>|null  $options  Available options for select type
     */
    public function __construct(
        public FlagKey $key,
        public FlagType $type,
        public string $default,
        public bool $defaultEnabled,
        public string $label,
        public string $description,
        public string $group,
        public string $groupLabel,
        public ?array $options = null,
        public ?string $pattern = null,
    ) {}
}
