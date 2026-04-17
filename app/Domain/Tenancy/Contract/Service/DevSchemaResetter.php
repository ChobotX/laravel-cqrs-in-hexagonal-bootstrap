<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Service;

interface DevSchemaResetter
{
    public function resetAll(): void;
}
