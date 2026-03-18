<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Contract\IdGenerator;
use Illuminate\Support\Str;

final readonly class UuidIdGenerator implements IdGenerator
{
    public function generate(): string
    {
        return Str::uuid()->toString();
    }
}
