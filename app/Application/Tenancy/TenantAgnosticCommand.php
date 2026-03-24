<?php

declare(strict_types=1);

namespace App\Application\Tenancy;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class TenantAgnosticCommand {}
