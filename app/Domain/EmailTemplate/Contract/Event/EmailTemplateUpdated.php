<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Application\Event\EntityUpdated;
use App\Application\Event\PropertyChange;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when email template updated in the EmailTemplate context; handled by registered domain event handlers.
 */
final readonly class EmailTemplateUpdated implements DomainEvent, EntityUpdated
{
    use DescribesAction;

    /** @param list<PropertyChange> $changes */
    public function __construct(
        /** Classifier string or type discriminator. */
        public string $templateType,
        /** BCP 47 locale code controlling formatting or translations. */
        public string $locale,
        /** Array for `changes`; see constructor PHPDoc for structural tags when present. */
        public array $changes,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    /** @return list<PropertyChange> */
    public function changes(): array
    {
        return $this->changes;
    }

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
