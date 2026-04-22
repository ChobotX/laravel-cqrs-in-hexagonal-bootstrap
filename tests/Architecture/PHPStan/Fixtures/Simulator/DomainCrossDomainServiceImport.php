<?php

declare(strict_types=1);

namespace App\Domain\PhpStanFixtures\Simulator;

use App\Domain\EmailTemplate\Contract\Service\EmailSender;

final readonly class DomainCrossDomainServiceImport
{
    public function __construct(private EmailSender $emailSender) {}
}
