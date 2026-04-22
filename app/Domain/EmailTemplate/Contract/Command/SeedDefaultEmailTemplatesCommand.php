<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Populates the active tenant schema with default email templates. Dispatched during tenant
 * bootstrap so cross-module orchestrators do not reach into the EmailTemplate seed service
 * directly.
 */
#[SkipPermissionCheck(reason: 'Dispatched internally during tenant bootstrap before any user exists')]
#[SkipDomainEvent(reason: 'Pure idempotent insertOrIgnore bootstrap; no domain event')]
final readonly class SeedDefaultEmailTemplatesCommand implements Command {}
