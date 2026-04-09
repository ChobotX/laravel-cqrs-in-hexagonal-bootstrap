<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateWithMetadata;

/** @implements Query<EmailTemplateWithMetadata> */
#[RequiresPermission('email_templates.templates.read')]
final readonly class GetEmailTemplateQuery implements Query
{
    public function __construct(
        public string $templateType,
        public string $locale,
    ) {}
}
