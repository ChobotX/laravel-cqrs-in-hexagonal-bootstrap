<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('settings.tenant.read')]
final class ShowPasswordRotationSettingsController
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route('settings.index', ['tab' => 'password-rotation']);
    }
}
