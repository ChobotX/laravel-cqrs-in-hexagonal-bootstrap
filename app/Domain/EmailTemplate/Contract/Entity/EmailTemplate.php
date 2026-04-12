<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Entity;

use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use DateTimeImmutable;

/**
 * Immutable read-model snapshot of a Email Template returned from queries in the EmailTemplate context.
 */
final readonly class EmailTemplate
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public EmailTemplateId $id,
        /** Field `type` for this contract; see module docs for validation rules. */
        public EmailTemplateType $type,
        /** BCP 47 locale code controlling formatting or translations. */
        public EmailTemplateLocale $locale,
        /** Field `subjectTemplate` for this contract; see module docs for validation rules. */
        public string $subjectTemplate,
        /** Field `bodyTemplate` for this contract; see module docs for validation rules. */
        public string $bodyTemplate,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $createdAt,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $updatedAt,
    ) {}
}
