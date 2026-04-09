<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Translation\Translator;

final class FakeTranslator implements Translator
{
    /** @var list<array{key: string, params: array<string, string|int>}> */
    public array $calls = [];

    public function translate(string $key, array $params = []): string
    {
        $this->calls[] = ['key' => $key, 'params' => $params];

        $result = $key;

        foreach ($params as $name => $value) {
            $result = str_replace(':'.$name, (string) $value, $result);
        }

        return $result;
    }

    public function locale(): string
    {
        return 'en';
    }
}
