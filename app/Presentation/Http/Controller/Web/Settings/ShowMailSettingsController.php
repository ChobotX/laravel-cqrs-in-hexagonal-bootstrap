<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Presentation\Http\Request\Web\Settings\ShowTenantSettingsRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('settings.tenant.read')]
final class ShowMailSettingsController
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route('settings.index', ['tab' => ShowTenantSettingsRequest::MAIL_TAB]);
    }
}
