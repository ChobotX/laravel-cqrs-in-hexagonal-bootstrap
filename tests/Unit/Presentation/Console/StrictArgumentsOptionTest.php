<?php

declare(strict_types=1);

use App\Presentation\Console\Exception\InvalidConsoleArgumentException;
use App\Presentation\Console\Trait\StrictArguments;

it('returns string option value', function (): void {
    $command = new class
    {
        use StrictArguments;

        public function argument(mixed $key = null): mixed
        {
            return '';
        }

        public function option(mixed $key = null): mixed
        {
            return 'value';
        }

        public function call(): ?string
        {
            return $this->nullableStringOption('test');
        }
    };

    expect($command->call())->toBe('value');
});

it('returns null for missing option', function (): void {
    $command = new class
    {
        use StrictArguments;

        public function argument(mixed $key = null): mixed
        {
            return '';
        }

        public function option(mixed $key = null): mixed
        {
            return null;
        }

        public function call(): ?string
        {
            return $this->nullableStringOption('test');
        }
    };

    expect($command->call())->toBeNull();
});

it('throws for non-string option value', function (): void {
    $command = new class
    {
        use StrictArguments;

        public function argument(mixed $key = null): mixed
        {
            return '';
        }

        public function option(mixed $key = null): mixed
        {
            return 123;
        }

        public function call(): ?string
        {
            return $this->nullableStringOption('test');
        }
    };

    $command->call();
})->throws(InvalidConsoleArgumentException::class);
