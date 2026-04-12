<?php

declare(strict_types=1);

namespace App\Contract\Command;

/**
 * Marker for a write-side message handled by the command bus. Concrete commands are immutable DTOs
 * under `App\Domain\{Context}\Contract\Command` and express a single state change or use case.
 */
interface Command {}
