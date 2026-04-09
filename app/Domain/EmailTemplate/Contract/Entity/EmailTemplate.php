<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Entity;

use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use DateTimeImmutable;

final readonly class EmailTemplate
{
    public function __construct(
        public EmailTemplateId $id,
        public EmailTemplateType $type,
        public EmailTemplateLocale $locale,
        public string $subjectTemplate,
        public string $bodyTemplate,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {}
}
