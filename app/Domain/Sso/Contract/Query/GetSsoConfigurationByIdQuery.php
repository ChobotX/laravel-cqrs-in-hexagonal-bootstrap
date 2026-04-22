<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;

/**
 * Loads a single SsoConfiguration for the admin detail/edit screen.
 *
 * @implements Query<SsoConfiguration>
 */
#[RequiresPermission('sso.management.read')]
final readonly class GetSsoConfigurationByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
