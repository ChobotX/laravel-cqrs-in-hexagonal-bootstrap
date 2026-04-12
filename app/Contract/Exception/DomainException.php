<?php

declare(strict_types=1);

namespace App\Contract\Exception;

use App\Contract\Translation\Translator;

/**
 * Domain-only failure surfaced to HTTP or CLI as a safe user message and HTTP status. Implementations live under
 * `App\Domain\{Context}\Exception` and must never wrap generic PHP exceptions for control flow.
 */
interface DomainException
{
    /**
     * Localized or templated message suitable for end users (no stack traces or internal identifiers unless intentional).
     */
    public function userMessage(Translator $translator): string;

    /** Suggested HTTP status when this exception maps to an API error response. */
    public function statusCode(): int;
}
