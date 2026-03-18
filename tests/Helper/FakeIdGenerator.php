<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\IdGenerator;

final class FakeIdGenerator implements IdGenerator
{
    /** @var list<string> */
    public array $generated = [];

    private int $counter = 0;

    public function generate(): string
    {
        $this->counter++;
        $id = sprintf('00000000-0000-0000-0000-%012d', $this->counter);
        $this->generated[] = $id;

        return $id;
    }
}
