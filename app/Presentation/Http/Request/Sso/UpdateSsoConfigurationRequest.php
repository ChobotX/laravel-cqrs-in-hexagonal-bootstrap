<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Sso;

use App\Domain\Sso\Contract\Enum\JitMode;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function explode;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function strtolower;
use function trim;

final class UpdateSsoConfigurationRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:128'],
            'enabled' => ['sometimes', 'boolean'],
            'enforce' => ['sometimes', 'boolean'],
            'jit_mode' => ['required', Rule::in(array_map(fn (JitMode $jitMode): string => $jitMode->value, JitMode::cases()))],
            'allowed_email_domains' => ['nullable', 'string'],
            'config' => ['nullable', 'array'],
        ];
    }

    public function displayName(): string
    {
        return $this->stringInput('display_name');
    }

    public function enabled(): bool
    {
        return $this->boolean('enabled');
    }

    public function enforce(): bool
    {
        return $this->boolean('enforce');
    }

    public function jitMode(): string
    {
        return $this->stringInput('jit_mode');
    }

    /** @return list<string> */
    public function allowedEmailDomains(): array
    {
        $raw = $this->input('allowed_email_domains');

        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }

        $parts = array_filter(
            array_map(fn (string $value): string => strtolower(trim($value)), explode(',', $raw)),
            fn (string $value): bool => $value !== '',
        );

        return array_values($parts);
    }

    /** @return array<string, scalar|array<int|string, mixed>|null> */
    public function configMap(): array
    {
        $raw = $this->input('config');

        if (! is_array($raw)) {
            return [];
        }

        /** @var array<string, scalar|array<int|string, mixed>|null> $cleaned */
        $cleaned = [];

        foreach (array_keys($raw) as $key) {
            $value = $raw[$key];

            if ($value === null || is_array($value) || is_string($value) || is_bool($value) || is_int($value) || is_float($value)) {
                $cleaned[(string) $key] = $value;
            }
        }

        return $cleaned;
    }

    private function stringInput(string $key): string
    {
        $value = $this->input($key);

        return is_string($value) ? $value : '';
    }
}
