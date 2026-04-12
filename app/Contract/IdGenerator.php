<?php

declare(strict_types=1);

namespace App\Contract;

/**
 * Produces opaque unique string identifiers without embedding domain meaning (UUID or similar, implementation-defined).
 */
interface IdGenerator
{
    /** New identifier; must not return an empty string. */
    public function generate(): string;
}
