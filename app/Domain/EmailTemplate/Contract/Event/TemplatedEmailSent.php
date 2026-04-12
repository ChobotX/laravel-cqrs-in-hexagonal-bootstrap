<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when templated email sent in the EmailTemplate context; handled by registered domain event handlers.
 */
final readonly class TemplatedEmailSent implements DomainEvent
{
    use DescribesAction;

    /**
     * @param  list<string>  $variableKeys
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $emailLogId,
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
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'email_log';
    }

    public function entityId(): string
    {
        return $this->emailLogId;
    }
}
