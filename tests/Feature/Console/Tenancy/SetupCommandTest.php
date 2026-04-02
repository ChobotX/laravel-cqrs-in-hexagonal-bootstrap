<?php

declare(strict_types=1);

use App\Presentation\Console\Tenancy\SetupCommand;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\Helper\FakeArtisanKernel;

it('runs setup successfully', function (): void {
    $fake = new FakeArtisanKernel;
    $command = new SetupCommand;

    $command->setLaravel($this->app);

    $input = new ArrayInput([]);
    $output = new OutputStyle($input, new NullOutput);
    $command->setInput($input);
    $command->setOutput($output);

    $exitCode = $command->handle($fake);

    expect($exitCode)->toBe(0)
        ->and($fake->calls)->toHaveCount(3)
        ->and($fake->calls[0])->toBe('tenant:drop-schemas')
        ->and($fake->calls[1])->toBe('migrate')
        ->and($fake->calls[2])->toBe('db:seed');
});
