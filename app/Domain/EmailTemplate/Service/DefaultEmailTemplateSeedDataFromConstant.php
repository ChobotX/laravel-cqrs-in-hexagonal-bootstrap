<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Service;

use App\Domain\EmailTemplate\Constant\DefaultEmailTemplates;
use App\Domain\EmailTemplate\Contract\Service\DefaultEmailTemplateSeedData;
use Override;

final readonly class DefaultEmailTemplateSeedDataFromConstant implements DefaultEmailTemplateSeedData
{
    #[Override]
    public function templatesByTypeLocaleKey(): array
    {
        return DefaultEmailTemplates::DEFAULTS;
    }
}
