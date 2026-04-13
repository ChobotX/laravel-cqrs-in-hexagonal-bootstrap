<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Service;

/**
 * Default HTML templates per type/locale (e.g. for new tenant schemas). Implemented in Domain so
 * Infrastructure can depend on this port instead of reaching into `DefaultEmailTemplates` constants directly.
 */
interface DefaultEmailTemplateSeedData
{
    /**
     * @return array<string, array{subject: string, body: string}> keyed as "{$type}:{$locale}"
     */
    public function templatesByTypeLocaleKey(): array;
}
