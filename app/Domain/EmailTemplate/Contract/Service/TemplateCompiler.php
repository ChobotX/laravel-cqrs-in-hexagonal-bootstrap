<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Service;

use App\Domain\EmailTemplate\Contract\ValueObject\RenderedEmail;

/**
 * Domain service contract for template compiler in the EmailTemplate bounded context.
 */
interface TemplateCompiler
{
    /**
     * Compile template strings with variables and wrap in HTML email layout.
     *
     * @param  array<string, string|null>  $variables
     *                                                 Contract operation `compile`; see infrastructure for behavior.
     */
    public function compile(string $subjectTemplate, string $bodyTemplate, array $variables): RenderedEmail;
}
