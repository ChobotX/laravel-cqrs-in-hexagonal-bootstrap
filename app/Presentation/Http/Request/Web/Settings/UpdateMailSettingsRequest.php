<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

use function array_map;

final class UpdateMailSettingsRequest extends FormRequest
{
    use HandlesFormRequest;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'use_custom' => ['sometimes', 'boolean'],
            'provider' => ['nullable', 'required_if:use_custom,true', Rule::in(array_map(static fn (MailProvider $mailProvider): string => $mailProvider->value, MailProvider::cases()))],
            'host' => ['nullable', 'required_if:use_custom,true', 'string', 'max:255'],
            'port' => ['nullable', 'required_if:use_custom,true', 'integer', 'min:1', 'max:65535'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'encryption' => ['nullable', Rule::in(['tls', 'ssl'])],
            'from_address' => ['nullable', 'required_if:use_custom,true', 'email:rfc', 'max:255'],
            'from_name' => ['nullable', 'required_if:use_custom,true', 'string', 'max:255'],
        ];
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'use_custom' => $this->boolean('use_custom'),
        ]);
    }
}
