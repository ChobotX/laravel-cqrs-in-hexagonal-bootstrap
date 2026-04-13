<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\PhpStanFixtures;

use App\Application\Bus\CommandBus;

final class BusNamespaceMayInjectCommandBus
{
    public function __construct(private CommandBus $commandBus) {}
}
