<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\ValueObject;

use function explode;
use function in_array;
use function strtolower;

/**
 * Lowercase list of email domains accepted by a SsoConfiguration when JitMode is auto-create.
 *
 * Empty list means no domain restriction.
 */
final readonly class AllowedEmailDomains
{
    private const int EMAIL_SPLIT_LIMIT = 2;

    /** @param list<string> $domains */
    public function __construct(
        /** Lowercase domain strings without `@`, e.g. `acme.com`. */
        public array $domains,
    ) {}

    public function isUnrestricted(): bool
    {
        return $this->domains === [];
    }

    public function permits(string $email): bool
    {
        if ($this->isUnrestricted()) {
            return true;
        }

        $parts = explode('@', strtolower($email), self::EMAIL_SPLIT_LIMIT);

        if (! isset($parts[1])) {
            return false;
        }

        return in_array($parts[1], $this->domains, true);
    }
}
