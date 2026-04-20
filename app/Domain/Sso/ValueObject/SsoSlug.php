<?php

declare(strict_types=1);

namespace App\Domain\Sso\ValueObject;

use App\Domain\Sso\Exception\InvalidSsoSlugException;
use Stringable;

use function preg_match;

/**
 * URL-safe slug used in `/auth/sso/{slug}` routes; lowercase letters, digits, hyphens.
 */
final readonly class SsoSlug implements Stringable
{
    private const string PATTERN = '/^[a-z0-9](?:[a-z0-9-]{0,62}[a-z0-9])?$/';

    public function __construct(public string $value)
    {
        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new InvalidSsoSlugException($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
