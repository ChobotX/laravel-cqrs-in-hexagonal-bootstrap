<?php

declare(strict_types=1);

namespace App\Application\Event;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class RetryPolicy
{
    /** @param list<int> $backoff Backoff delays in seconds between retries */
    public function __construct(
        public int $tries,
        public array $backoff,
        public int $timeout,
    ) {}
}
