<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class EmailTemplateReset implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $templateType,
        public string $locale,
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
