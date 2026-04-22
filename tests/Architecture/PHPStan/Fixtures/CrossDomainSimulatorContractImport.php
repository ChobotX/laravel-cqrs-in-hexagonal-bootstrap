<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;

final class CrossDomainSimulatorContractImport
{
    public function __construct(private SendTemplatedEmailCommand $sendTemplatedEmailCommand) {}
}
