<?php

declare(strict_types=1);

namespace Tests\Helper;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bus\PendingDispatch;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FakeArtisanKernel implements Kernel
{
    /** @var list<string> */
    public array $calls = [];

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface|null  $output
     */
    public function handle($input, $output = null): int
    {
        return 0;
    }

    /**
     * @param  array<mixed>  $parameters
     * @param  OutputInterface|null  $outputBuffer
     */
    public function call($command, array $parameters = [], $outputBuffer = null): int
    {
        $this->calls[] = $command;

        return 0;
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function queue($command, array $parameters = []): PendingDispatch
    {
        return new PendingDispatch($command);
    }

    /**
     * @return array<string, \Illuminate\Console\Command>
     */
    public function all(): array
    {
        return [];
    }

    public function output(): string
    {
        return '';
    }

    public function bootstrap(): void {}

    /**
     * @param  InputInterface  $input
     */
    public function terminate($input, $status): void {}
}
