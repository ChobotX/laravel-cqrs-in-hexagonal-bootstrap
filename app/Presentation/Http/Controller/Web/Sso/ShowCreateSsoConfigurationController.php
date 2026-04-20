<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sso;

use App\Application\Authorization\RequiresPermission;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use Illuminate\View\View;

use function view;

#[RequiresPermission('sso.management.create')]
final readonly class ShowCreateSsoConfigurationController
{
    public function __invoke(): View
    {
        return view('sso.create', [
            'providerTypes' => ProviderType::cases(),
            'jitModes' => JitMode::cases(),
        ]);
    }
}
