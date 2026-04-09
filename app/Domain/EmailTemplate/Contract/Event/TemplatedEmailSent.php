<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class TemplatedEmailSent implements DomainEvent
{
    /**
     * @param  list<string>  $variableKeys
     */
    public function __construct(
        public string $emailLogId,
        public string $templateType,
        public string $locale,
        public string $recipientId,
        public string $recipientEmail,
        public string $renderedSubject,
        public string $renderedBodyMasked,
        public array $variableKeys,
        public ?string $traceId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
