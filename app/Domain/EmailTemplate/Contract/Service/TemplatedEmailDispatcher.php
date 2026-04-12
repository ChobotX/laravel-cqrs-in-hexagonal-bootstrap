<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Service;

/**
 * Domain service contract for templated email in the EmailTemplate bounded context.
 */
interface TemplatedEmailDispatcher
{
    /**
     * Look up template, render with variables, send HTML email, log to email_logs.
     *
     * @param  array<string, string|null>  $variables
     *                                                 Executes the side effect synchronously.
     */
    public function dispatch(string $userId, string $templateType, string $locale, array $variables): void;
}
