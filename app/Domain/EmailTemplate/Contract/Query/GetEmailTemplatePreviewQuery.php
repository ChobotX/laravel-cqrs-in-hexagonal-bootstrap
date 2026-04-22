<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\EmailTemplate\Contract\ValueObject\RenderedEmail;

/**
 * Query for get email template preview in the EmailTemplate bounded context; dispatched through the query bus.
 *
 * @implements Query<RenderedEmail>
 */
#[RequiresPermission('email_templates.templates.read')]
final readonly class GetEmailTemplatePreviewQuery implements Query
{
    public function __construct(
        /** Classifier string or type discriminator. */
        public string $templateType,
        /** BCP 47 locale code controlling formatting or translations. */
        public string $locale,
        /** Field `subjectTemplate` for this contract; see module docs for validation rules. */
        public string $subjectTemplate,
        /** Field `bodyTemplate` for this contract; see module docs for validation rules. */
        public string $bodyTemplate,
    ) {}
}
