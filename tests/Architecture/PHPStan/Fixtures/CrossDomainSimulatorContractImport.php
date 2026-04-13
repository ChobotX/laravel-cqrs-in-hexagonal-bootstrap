<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use App\Domain\EmailTemplate\Contract\Service\EmailSender;

final class CrossDomainSimulatorContractImport
{
    public function __construct(private EmailSender $emailSender) {}
}
