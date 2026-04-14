<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;

/**
 * @implements Query<PasswordRotationSettings>
 */
#[RequiresPermission('settings.tenant.read')]
final readonly class GetPasswordRotationSettingsQuery implements Query {}
