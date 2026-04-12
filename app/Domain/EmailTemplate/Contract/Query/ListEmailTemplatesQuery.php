<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for list email templates in the EmailTemplate bounded context; dispatched through the query bus.
 *
 * @implements Query<list<array{type: string, name: string, description: string, locales: list<string>, updatedAt: ?string}>>
 */
#[RequiresPermission('email_templates.templates.read')]
final readonly class ListEmailTemplatesQuery implements Query {}
