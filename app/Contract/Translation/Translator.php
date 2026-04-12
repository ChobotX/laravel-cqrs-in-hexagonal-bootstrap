<?php

declare(strict_types=1);

namespace App\Contract\Translation;

/**
 * Resolves message keys to localized strings so domain exceptions and services stay free of Laravel helpers.
 */
interface Translator
{
    /**
     * Looks up `$key` for the active locale and interpolates placeholders from `$params`.
     *
     * @param  array<string, string|int>  $params
     */
    public function translate(string $key, array $params = []): string;

    /** BCP 47 or application locale code used for the current request. */
    public function locale(): string;
}
