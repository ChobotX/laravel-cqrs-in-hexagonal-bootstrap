<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Service;

interface TemplatedEmailDispatcher
{
    /**
     * Look up template, render with variables, send HTML email, log to email_logs.
     *
     * @param  array<string, string|null>  $variables
     */
    public function dispatch(string $userId, string $templateType, string $locale, array $variables): void;
}
