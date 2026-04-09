<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\EmailTemplate;

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use DateTimeImmutable;

final readonly class EmailTemplateMapper
{
    public function toDomain(EmailTemplateModel $emailTemplateModel): EmailTemplate
    {
        return new EmailTemplate(
            id: new EmailTemplateId($emailTemplateModel->id),
            type: new EmailTemplateType($emailTemplateModel->type),
            locale: new EmailTemplateLocale($emailTemplateModel->locale),
            subjectTemplate: $emailTemplateModel->subject_template,
            bodyTemplate: $emailTemplateModel->body_template,
            createdAt: new DateTimeImmutable($emailTemplateModel->created_at->toIso8601String()),
            updatedAt: new DateTimeImmutable($emailTemplateModel->updated_at->toIso8601String()),
        );
    }
}
