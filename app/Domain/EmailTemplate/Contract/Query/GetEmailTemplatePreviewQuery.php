<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\EmailTemplate\Contract\ValueObject\RenderedEmail;

/** @implements Query<RenderedEmail> */
#[RequiresPermission('email_templates.templates.read')]
final readonly class GetEmailTemplatePreviewQuery implements Query
{
    public function __construct(
        public string $templateType,
        public string $locale,
        public string $subjectTemplate,
        public string $bodyTemplate,
    ) {}
}
