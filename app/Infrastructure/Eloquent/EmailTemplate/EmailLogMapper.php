<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\EmailTemplate;

use App\Domain\EmailTemplate\Contract\Entity\EmailLog;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailLogId;
use DateTimeImmutable;

final readonly class EmailLogMapper
{
    public function toDomain(EmailLogModel $emailLogModel): EmailLog
    {
        return new EmailLog(
            id: new EmailLogId($emailLogModel->id),
            templateType: $emailLogModel->template_type,
            locale: $emailLogModel->locale,
            recipientId: $emailLogModel->recipient_id,
            recipientEmail: $emailLogModel->recipient_email,
            renderedSubject: $emailLogModel->rendered_subject,
            renderedBodyMasked: $emailLogModel->rendered_body_masked,
            variableKeys: $emailLogModel->variable_keys,
            traceId: $emailLogModel->trace_id,
            sentAt: new DateTimeImmutable($emailLogModel->sent_at->toIso8601String()),
        );
    }
}
