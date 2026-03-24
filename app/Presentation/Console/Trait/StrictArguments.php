<?php

declare(strict_types=1);

namespace App\Presentation\Console\Trait;

use App\Presentation\Console\Exception\InvalidConsoleArgumentException;

use function is_string;

trait StrictArguments
{
    abstract public function argument(mixed $key = null);

    abstract public function option(mixed $key = null);

    protected function stringArgument(string $key): string
    {
        $value = $this->argument($key);

        if (! is_string($value)) {
            throw InvalidConsoleArgumentException::expectedString($key);
        }

        return $value;
    }

    protected function nullableStringOption(string $key): ?string
    {
        $value = $this->option($key);

        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw InvalidConsoleArgumentException::expectedString($key);
        }

        return $value;
    }
}
