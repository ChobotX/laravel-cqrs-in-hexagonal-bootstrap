<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Tenancy\Contract\Command\UpdateTenantMailTransportCommand;
use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Presentation\Http\Request\Web\Settings\ShowTenantSettingsRequest;
use App\Presentation\Http\Request\Web\Settings\UpdateMailSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Context;

#[RequiresPermission('settings.tenant.update')]
final readonly class UpdateMailSettingsController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateMailSettingsRequest $updateMailSettingsRequest): RedirectResponse
    {
        $tenantId = Context::get('tenant_id');

        if (! is_string($tenantId)) {
            abort(HttpStatus::FORBIDDEN);
        }

        $this->commandBus->dispatch(new UpdateTenantMailTransportCommand(
            tenantId: $tenantId,
            useCustom: $updateMailSettingsRequest->boolean('use_custom'),
            provider: $this->resolveProvider($updateMailSettingsRequest),
            host: $this->stringInput($updateMailSettingsRequest, 'host'),
            port: $updateMailSettingsRequest->filled('port') ? $updateMailSettingsRequest->integer('port') : null,
            username: $this->stringInput($updateMailSettingsRequest, 'username'),
            password: $this->stringInput($updateMailSettingsRequest, 'password'),
            encryption: $this->stringInput($updateMailSettingsRequest, 'encryption'),
            fromAddress: $this->stringInput($updateMailSettingsRequest, 'from_address'),
            fromName: $this->stringInput($updateMailSettingsRequest, 'from_name'),
        ));

        return redirect()->route('settings.index', ['tab' => ShowTenantSettingsRequest::MAIL_TAB])
            ->with('success', __('messages.settings.mail_updated'));
    }

    private function resolveProvider(UpdateMailSettingsRequest $updateMailSettingsRequest): ?MailProvider
    {
        $value = $updateMailSettingsRequest->input('provider');

        return is_string($value) ? MailProvider::tryFrom($value) : null;
    }

    private function stringInput(UpdateMailSettingsRequest $updateMailSettingsRequest, string $key): ?string
    {
        $value = $updateMailSettingsRequest->input($key);

        return is_string($value) ? $value : null;
    }
}
