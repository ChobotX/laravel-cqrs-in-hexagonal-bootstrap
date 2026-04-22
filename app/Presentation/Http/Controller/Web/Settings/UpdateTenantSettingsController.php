<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Tenancy\Contract\Command\UpdateTenantSettingsCommand;
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

        if (! is_string($tenantId)) {
            abort(HttpStatus::FORBIDDEN);
        }

        $logo = $updateTenantSettingsRequest->file('logo');

        /** @var string|null $displayTimezone */
        $displayTimezone = $updateTenantSettingsRequest->validated('display_timezone');

        $this->commandBus->dispatch(new UpdateTenantSettingsCommand(
            tenantId: $tenantId,
            name: $updateTenantSettingsRequest->string('name')->toString(),
            logo: $logo instanceof UploadedFile ? $logo : null,
            removeLogo: $updateTenantSettingsRequest->boolean('remove_logo'),
            displayTimezone: is_string($displayTimezone)
                ? $displayTimezone
                : null,
        ));

        return redirect()->route('settings.index')->with('success', __('messages.settings.updated'));
    }
}
