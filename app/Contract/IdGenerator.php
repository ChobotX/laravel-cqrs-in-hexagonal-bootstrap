<?php

declare(strict_types=1);

namespace App\Contract;

interface IdGenerator
{
    public function generate(): string;
}
