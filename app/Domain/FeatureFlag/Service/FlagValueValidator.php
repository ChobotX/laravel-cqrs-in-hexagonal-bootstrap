<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Service;

use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Exception\InvalidFlagValueException;

final readonly class FlagValueValidator
{
    private const string BOOLEAN_TRUE = '1';

    private const string BOOLEAN_FALSE = '0';

    public function validate(FlagDefinition $flagDefinition, string $value): void
    {
        match ($flagDefinition->type) {
            FlagType::Boolean => $this->validateBoolean($flagDefinition, $value),
            FlagType::Select => $this->validateSelect($flagDefinition, $value),
            FlagType::Input => $this->validateInput($flagDefinition, $value),
        };
    }

    private function validateBoolean(FlagDefinition $flagDefinition, string $value): void
    {
        if ($value !== self::BOOLEAN_TRUE && $value !== self::BOOLEAN_FALSE) {
            throw new InvalidFlagValueException(
                $flagDefinition->key->value,
                $value,
                'Boolean flags accept only "0" or "1".',
            );
        }
    }

    private function validateSelect(FlagDefinition $flagDefinition, string $value): void
    {
        if ($flagDefinition->options === null || ! in_array($value, $flagDefinition->options, true)) {
            throw new InvalidFlagValueException(
                $flagDefinition->key->value,
                $value,
                sprintf('Value must be one of: %s.', implode(', ', $flagDefinition->options ?? [])),
            );
        }
    }

    private function validateInput(FlagDefinition $flagDefinition, string $value): void
    {
        if ($flagDefinition->pattern !== null && preg_match($flagDefinition->pattern, $value) !== 1) {
            throw new InvalidFlagValueException(
                $flagDefinition->key->value,
                $value,
                sprintf('Value must match pattern: %s.', $flagDefinition->pattern),
            );
        }
    }
}
