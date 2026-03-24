<?php

declare(strict_types=1);

namespace Tests\Helper;

use Illuminate\Contracts\Console\Kernel;

final class FakeArtisanKernel implements Kernel
{
    /** @var list<string> */
    public array $calls = [];

    public function handle($input, $output = null): int
    {
        return 0;
    }

    public function call($command, array $parameters = [], $outputBuffer = null): int
    {
        /** @var string $name */
        $name = $command;
        $this->calls[] = $name;

        return 0;
    }

    public function queue($command, array $parameters = []): mixed
    {
        return null;
    }

    /** @return array<string, \Illuminate\Console\Command> */
    public function all(): array
    {
        return [];
    }

    public function output(): string
    {
        return '';
    }

    public function bootstrap(): void {}

    public function terminate($input, $status): void {}
}
