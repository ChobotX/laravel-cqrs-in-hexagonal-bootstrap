<?php

declare(strict_types=1);

namespace App\Contract\Event;

use DateTimeImmutable;

/**
 * Something meaningful that happened in the domain, published for asynchronous or ancillary processing.
 * Events are immutable facts; handlers must not treat them as commands.
 */
interface DomainEvent
{
    /** When the domain considers the event to have happened (not necessarily DB commit time). */
    public function occurredAt(): DateTimeImmutable;

    /**
     * Stable type key for the aggregate or resource (e.g. table or entity name) so subscribers can filter.
     */
    public function entityType(): string;

    /** Identifier of the affected entity within the type returned by `entityType()`. */
    public function entityId(): string;

    /** Short human-oriented label for logs and UI (translation key or fixed phrase, depending on implementation). */
    public function actionLabel(): string;
}
