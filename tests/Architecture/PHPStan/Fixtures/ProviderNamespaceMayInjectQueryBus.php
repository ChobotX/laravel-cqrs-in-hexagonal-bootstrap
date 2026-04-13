<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider\PhpStanFixtures;

use App\Application\Bus\QueryBus;

final class ProviderNamespaceMayInjectQueryBus
{
    public function __construct(private QueryBus $queryBus) {}
}
