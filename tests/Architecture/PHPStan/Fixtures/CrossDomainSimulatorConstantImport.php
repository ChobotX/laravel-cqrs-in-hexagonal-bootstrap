<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use App\Domain\EmailTemplate\Constant\DefaultEmailTemplates;

final class CrossDomainSimulatorConstantImport
{
    public function __construct(private DefaultEmailTemplates $ignored) {}
}
