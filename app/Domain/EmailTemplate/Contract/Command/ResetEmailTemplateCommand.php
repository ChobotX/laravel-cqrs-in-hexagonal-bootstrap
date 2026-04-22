<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for reset email template in the EmailTemplate bounded context; dispatched through the command bus.
 */
#[RequiresPermission('email_templates.templates.update')]
final readonly class ResetEmailTemplateCommand implements Command
{
    public function __construct(
        /** Classifier string or type discriminator. */
        public string $templateType,
        /** BCP 47 locale code controlling formatting or translations. */
        public string $locale,
    ) {}
}
