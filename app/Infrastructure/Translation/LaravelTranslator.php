<?php

declare(strict_types=1);

namespace App\Infrastructure\Translation;

use App\Contract\Translation\Translator;

use function is_string;

final class LaravelTranslator implements Translator
{
    /**
     * @param  array<string, string|int>  $params
     */
    public function translate(string $key, array $params = []): string
    {
        $message = __($key, $params);

        if (! is_string($message)) {
            throw UnexpectedTranslationTypeException::expectedString($key);
        }

        return $message;
    }

    public function locale(): string
    {
        return app()->getLocale();
    }
}
