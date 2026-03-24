<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Root;

use App\Presentation\Http\Request\FormRequest;
use Illuminate\Validation\Rule;

final class RegisterTenantFormRequest extends FormRequest
{
    /** @return array<string, array<string|\Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\Unique>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', Rule::unique('landlord.tenants', 'slug')],
            'domain' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', Rule::unique('landlord.tenant_domains', 'domain')],
        ];
    }
}
