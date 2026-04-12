<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('email_templates.templates.update')]
final readonly class ResetEmailTemplateCommand implements Command
{
    public function __construct(
        public string $templateType,
        public string $locale,
    ) {}
}
