<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Contract\Http\HttpStatus;
use App\Domain\User\Contract\Command\UpdatePasswordRotationSettingsCommand;
use App\Presentation\Http\Request\Web\Settings\UpdatePasswordRotationSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Context;

#[RequiresPermission('settings.tenant.update')]
final readonly class UpdatePasswordRotationSettingsController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdatePasswordRotationSettingsRequest $updatePasswordRotationSettingsRequest): RedirectResponse
    {
        $tenantId = Context::get('tenant_id');

        if (! is_string($tenantId)) {
            abort(HttpStatus::FORBIDDEN);
        }

        $this->commandBus->dispatch(new UpdatePasswordRotationSettingsCommand(
            rotationEnabled: $updatePasswordRotationSettingsRequest->boolean('rotation_enabled'),
            maxAgeDays: $updatePasswordRotationSettingsRequest->filled('max_age_days') ? $updatePasswordRotationSettingsRequest->integer('max_age_days') : null,
            historyCount: $updatePasswordRotationSettingsRequest->integer('history_count'),
        ));

        return redirect()->route('settings.index', ['tab' => 'password-rotation'])
            ->with('success', __('messages.settings.password_rotation_updated'));
    }
}
