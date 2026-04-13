<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Service;

use App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent;

/**
 * Domain service contract for templated email in the EmailTemplate bounded context.
 */
interface TemplatedEmailDispatcher
{
    /**
     * Looks up the template, renders with variables, sends HTML email, and returns the audit event
     * the caller must collect.
     *
     * @param  array<string, string|null>  $variables
     */
    public function dispatch(string $userId, string $templateType, string $locale, array $variables): TemplatedEmailSent;
}
