<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('email_templates.templates.update')]
final readonly class ResetEmailTemplateCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $templateType,
        public string $locale,
    ) {}

    public function auditEntityType(): string
    {
        return 'email_template';
    }

    public function auditEntityId(): string
    {
        return $this->templateType.':'.$this->locale;
    }
}
