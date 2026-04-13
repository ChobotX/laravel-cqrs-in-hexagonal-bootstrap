<?php

declare(strict_types=1);

namespace App\Domain\PhpStanFixtures\Simulator;

use App\Domain\User\ValueObject\Email;

final readonly class DomainCrossDomainBadImport
{
    public function __construct(private Email $email) {}
}
