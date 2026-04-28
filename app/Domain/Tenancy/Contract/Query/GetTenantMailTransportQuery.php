<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;

/**
 * Query for the active per-tenant mail transport (custom override, or default when none is configured).
 *
 * @implements Query<MailTransport>
 */
#[SkipPermissionCheck(reason: 'Read by the email dispatcher inside command pipelines, not by user action')]
final readonly class GetTenantMailTransportQuery implements Query {}
