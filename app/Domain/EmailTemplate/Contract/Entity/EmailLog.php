<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Entity;

use App\Domain\EmailTemplate\Contract\ValueObject\EmailLogId;
use DateTimeImmutable;

final readonly class EmailLog
{
    /**
     * @param  list<string>  $variableKeys
     */
    public function __construct(
        public EmailLogId $id,
        public string $templateType,
        public string $locale,
        public string $recipientId,
        public string $recipientEmail,
        public string $renderedSubject,
        public string $renderedBodyMasked,
        public array $variableKeys,
        public ?string $traceId,
        public DateTimeImmutable $sentAt,
    ) {}
}
