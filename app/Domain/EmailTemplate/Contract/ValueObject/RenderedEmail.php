<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\ValueObject;

/**
 * Contract-level value object for rendered email used across EmailTemplate commands, queries, and events.
 */
final readonly class RenderedEmail
{
    public function __construct(
        /** Field `subject` for this contract; see module docs for validation rules. */
        public string $subject,
        /** Field `htmlBody` for this contract; see module docs for validation rules. */
        public string $htmlBody,
    ) {}
}
