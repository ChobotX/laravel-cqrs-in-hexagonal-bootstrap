<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\ValueObject;

use App\Domain\Sso\Exception\InvalidSsoConfigurationIdException;
use Stringable;

use function preg_match;

/**
 * Stable identifier (UUID) for a tenant SsoConfiguration row.
 */
final readonly class SsoConfigurationId implements Stringable
{
    private const string UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function __construct(
        /** UUID string identifying the SsoConfiguration aggregate. */
        public string $value,
    ) {
        if (preg_match(self::UUID_PATTERN, $value) !== 1) {
            throw new InvalidSsoConfigurationIdException($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
