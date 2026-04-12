<?php

declare(strict_types=1);

namespace App\Contract\Event;

use DateTimeImmutable;

interface DomainEvent
{
    public function occurredAt(): DateTimeImmutable;

    public function entityType(): string;

    public function entityId(): string;

    public function actionLabel(): string;
}
