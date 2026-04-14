<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Application\Authorization\RequiresPermission;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('settings.tenant.read')]
final readonly class ShowTwoFactorSettingsController
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route('settings.index', ['tab' => 'two-factor']);
    }
}
