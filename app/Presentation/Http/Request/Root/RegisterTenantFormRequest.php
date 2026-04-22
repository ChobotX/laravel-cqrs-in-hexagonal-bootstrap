<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Root;

use App\Domain\Tenancy\Contract\Command\RegisterTenantWithAdminCommand;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RegisterTenantFormRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string|\Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\Unique>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', Rule::unique('landlord.tenants', 'slug')],
            'domain' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', Rule::unique('landlord.tenant_domains', 'domain')],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
        ];
    }

    public function toCommand(string $adminId): RegisterTenantWithAdminCommand
    {
        return new RegisterTenantWithAdminCommand(
            name: $this->string('name')->toString(),
            slug: $this->string('slug')->toString(),
            domain: $this->string('domain')->toString(),
            adminId: $adminId,
            adminName: $this->string('admin_name')->toString(),
            adminEmail: $this->string('admin_email')->toString(),
        );
    }
}
