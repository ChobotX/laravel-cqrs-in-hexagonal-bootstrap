<?php

declare(strict_types=1);

namespace App\Contract\Logging;

/**
 * Structured logging port so domain and infrastructure stay free of concrete logging libraries.
 */
interface Logger
{
    /**
     * Verbose diagnostic messages; may be discarded in production.
     *
     * @param  array<string, mixed>  $context
     */
    public function debug(string $message, array $context = []): void;

    /**
     * Normal operational messages.
     *
     * @param  array<string, mixed>  $context
     */
    public function info(string $message, array $context = []): void;

    /**
     * Potentially harmful situations that are not yet errors.
     *
     * @param  array<string, mixed>  $context
     */
    public function warning(string $message, array $context = []): void;

    /**
     * Failures and exceptions worth immediate attention.
     *
     * @param  array<string, mixed>  $context
     */
    public function error(string $message, array $context = []): void;
}
