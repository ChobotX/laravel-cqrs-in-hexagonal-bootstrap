<?php

declare(strict_types=1);

use App\Presentation\Console\Exception\InvalidConsoleArgumentException;
use App\Presentation\Console\Trait\StrictArguments;

it('throws when argument is not a string', function (): void {
    $command = new class
    {
        use StrictArguments;

        public function argument(mixed $key = null): mixed
        {
            return 123;
        }

        public function call(): string
        {
            return $this->stringArgument('name');
        }
    };

    $command->call();
})->throws(InvalidConsoleArgumentException::class, "Expected argument/option 'name' to be a string.");
