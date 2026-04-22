<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\User\Contract\ValueObject\TwoFactorSettings;

/**
 * @implements Query<TwoFactorSettings>
 */
#[RequiresPermission('settings.tenant.read')]
final readonly class GetTwoFactorSettingsQuery implements Query {}
