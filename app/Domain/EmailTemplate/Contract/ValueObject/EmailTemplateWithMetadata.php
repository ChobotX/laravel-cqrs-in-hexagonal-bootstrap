<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\ValueObject;

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;

/**
 * Contract-level value object for email template with metadata used across EmailTemplate commands, queries, and events.
 */
final readonly class EmailTemplateWithMetadata
{
    /**
     * @param  array<string, array{description: string, sensitive: bool, sample: string}>  $variables
     */
    public function __construct(
        /** Field `template` for this contract; see module docs for validation rules. */
        public EmailTemplate $template,
        /** Human-visible label or title. */
        public string $typeName,
        /** Longer human-readable explanation for admin UI or emails. */
        public string $typeDescription,
        /** Array for `variables`; see constructor PHPDoc for structural tags when present. */
        public array $variables,
    ) {}
}
