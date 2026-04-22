<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\EventHandler;

use App\Contract\Attribute\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\EmailTemplate\Contract\Entity\EmailLog;
use App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent;
use App\Domain\EmailTemplate\Contract\Repository\EmailLogRepository;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailLogId;

/** @implements DomainEventHandler<TemplatedEmailSent> */
#[RetryPolicy(tries: 3, backoff: [10, 30, 60], timeout: 30)]
final readonly class LogEmailOnSent implements DomainEventHandler
{
    public function __construct(
        private EmailLogRepository $emailLogRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $this->emailLogRepository->create(new EmailLog(
            id: new EmailLogId($domainEvent->emailLogId),
            templateType: $domainEvent->templateType,
            locale: $domainEvent->locale,
            recipientId: $domainEvent->recipientId,
            recipientEmail: $domainEvent->recipientEmail,
            renderedSubject: $domainEvent->renderedSubject,
            renderedBodyMasked: $domainEvent->renderedBodyMasked,
            variableKeys: $domainEvent->variableKeys,
            traceId: $domainEvent->traceId,
            sentAt: $domainEvent->occurredAt,
        ));
    }
}
