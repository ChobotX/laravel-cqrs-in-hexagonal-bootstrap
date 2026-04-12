<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateWithMetadata;

/**
 * Query for get email template in the EmailTemplate bounded context; dispatched through the query bus.
 *
 * @implements Query<EmailTemplateWithMetadata>
 */
#[RequiresPermission('email_templates.templates.read')]
final readonly class GetEmailTemplateQuery implements Query
{
    public function __construct(
        /** Classifier string or type discriminator. */
        public string $templateType,
        /** BCP 47 locale code controlling formatting or translations. */
        public string $locale,
    ) {}
}
