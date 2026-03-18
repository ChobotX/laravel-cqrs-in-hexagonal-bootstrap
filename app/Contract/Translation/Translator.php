<?php

declare(strict_types=1);

namespace App\Contract\Translation;

interface Translator
{
    /**
     * @param  array<string, string|int>  $params
     */
    public function translate(string $key, array $params = []): string;
}
