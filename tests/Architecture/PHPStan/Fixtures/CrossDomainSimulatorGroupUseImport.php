<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use App\Domain\EmailTemplate\Constant\{DefaultEmailTemplates, EmailTemplateTypes};

final class CrossDomainSimulatorGroupUseImport
{
    public function __construct(
        private DefaultEmailTemplates $ignored,
        private EmailTemplateTypes $ignored2,
    ) {}
}
