<?php

declare(strict_types=1);

namespace App\Contract\Event;

/**
 * Implemented by delete events so subscribers can correlate teardown logic with the removed entity.
 */
interface EntityDeleted
{
    /** Primary key or stable id of the deleted entity as used in commands and queries. */
    public function entityId(): string;

    /** Type discriminator matching resource_type in record_shares and similar polymorphic tables. */
    public function entityType(): string;
}
