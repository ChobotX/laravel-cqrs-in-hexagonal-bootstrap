<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\QueryBus;
use App\Domain\User\Contract\Query\GetTotpSetupQuery;
use App\Domain\User\Contract\Query\GetTwoFactorStatusQuery;
use App\Presentation\Support\TotpQrSvgGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

#[SkipPermissionCheck('Authenticated users can manage own two-factor settings')]
final readonly class ShowOwnTwoFactorSettingsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        $userId = (string) Auth::id();
        $twoFactorUiStatus = $this->queryBus->dispatch(new GetTwoFactorStatusQuery);
        $totpSetup = $this->queryBus->dispatch(new GetTotpSetupQuery($userId));

        $totpQrSvg = null;
        if (is_string($totpSetup->otpauthUri) && $totpSetup->otpauthUri !== '') {
            $totpQrSvg = (new TotpQrSvgGenerator)->fromOtpauthUri($totpSetup->otpauthUri);
        }

        return view('settings.two-factor', [
            'status' => $twoFactorUiStatus,
            'totpSetup' => $totpSetup,
            'totpQrSvg' => $totpQrSvg,
        ]);
    }
}
