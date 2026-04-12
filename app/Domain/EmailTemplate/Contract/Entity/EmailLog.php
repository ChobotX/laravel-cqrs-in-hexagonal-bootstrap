<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Entity;

use App\Domain\EmailTemplate\Contract\ValueObject\EmailLogId;
use DateTimeImmutable;

/**
 * Immutable read-model snapshot of a Email Log returned from queries in the EmailTemplate context.
 */
final readonly class EmailLog
{
    /**
     * @param  list<string>  $variableKeys
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public EmailLogId $id,
        /** Classifier string or type discriminator. */
        public string $templateType,
        /** BCP 47 locale code controlling formatting or translations. */
        public string $locale,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $recipientId,
        /** Email address used for lookup, delivery, or authentication flows. */
        public string $recipientEmail,
        /** Field `renderedSubject` for this contract; see module docs for validation rules. */
        public string $renderedSubject,
        /** Field `renderedBodyMasked` for this contract; see module docs for validation rules. */
        public string $renderedBodyMasked,
        /** Array for `variableKeys`; see constructor PHPDoc for structural tags when present. */
        public array $variableKeys,
        /** Optional trace identifier when absent. */
        public ?string $traceId,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $sentAt,
    ) {}
}
