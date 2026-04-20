<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;

/**
 * Returns all SsoConfigurations for the current tenant (admin view).
 *
 * @implements Query<list<SsoConfiguration>>
 */
#[RequiresPermission('sso.management.read')]
final readonly class ListSsoConfigurationsQuery implements Query {}
