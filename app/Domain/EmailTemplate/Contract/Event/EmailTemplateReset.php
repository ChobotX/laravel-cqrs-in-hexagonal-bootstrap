<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when email template reset in the EmailTemplate context; handled by registered domain event handlers.
 */
final readonly class EmailTemplateReset implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Classifier string or type discriminator. */
        public string $templateType,
        /** BCP 47 locale code controlling formatting or translations. */
        public string $locale,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'email_template';
    }

    public function entityId(): string
    {
        return $this->templateType.':'.$this->locale;
    }
}
