<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;

/**
 * Performs a non-interactive probe of an SsoConfiguration (discovery URL / metadata fetch).
 *
 * @implements Query<SsoConnectionTestResult>
 */
#[RequiresPermission('sso.management.test')]
final readonly class TestSsoConfigurationQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
