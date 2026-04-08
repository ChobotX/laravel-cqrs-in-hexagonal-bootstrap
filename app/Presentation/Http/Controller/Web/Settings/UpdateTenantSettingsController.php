<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Tenancy\Command\UpdateTenantSettings\UpdateTenantSettingsCommand;
use App\Presentation\Http\Request\Web\Settings\UpdateTenantSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Context;

#[RequiresPermission('settings.tenant.update')]
final readonly class UpdateTenantSettingsController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateTenantSettingsRequest $updateTenantSettingsRequest): RedirectResponse
    {
        $tenantId = Context::get('tenant_id');
        assert(is_string($tenantId));

        $logo = $updateTenantSettingsRequest->file('logo');

        $this->commandBus->dispatch(new UpdateTenantSettingsCommand(
            tenantId: $tenantId,
            name: $updateTenantSettingsRequest->string('name')->toString(),
            logo: $logo instanceof UploadedFile ? $logo : null,
            removeLogo: $updateTenantSettingsRequest->boolean('remove_logo'),
        ));

        return redirect()->route('settings.index')->with('success', __('messages.settings.updated'));
    }
}
