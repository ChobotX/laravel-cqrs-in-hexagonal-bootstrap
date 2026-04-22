<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Tenancy\Contract\Query\GetTenantSettingsQuery;
use App\Domain\User\Contract\Query\GetPasswordRotationSettingsQuery;
use App\Domain\User\Contract\Query\GetTwoFactorSettingsQuery;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use App\Presentation\Http\Request\Web\Settings\ShowTenantSettingsRequest;
use DateTimeZone;
use Illuminate\Support\Facades\Context;
use Illuminate\View\View;

#[RequiresPermission('settings.tenant.read')]
final readonly class ShowTenantSettingsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(ShowTenantSettingsRequest $showTenantSettingsRequest): View
    {
        $tenantId = Context::get('tenant_id');

        if (! is_string($tenantId)) {
            abort(HttpStatus::FORBIDDEN);
        }

        $tenantSettings = $this->queryBus->dispatch(new GetTenantSettingsQuery(
            tenantId: $tenantId,
        ));
        $passwordRotationSettings = $this->queryBus->dispatch(new GetPasswordRotationSettingsQuery);
        $twoFactorSettings = $this->queryBus->dispatch(new GetTwoFactorSettingsQuery);

        return view('settings.tenant', [
            'settings' => $tenantSettings,
            'ianaTimezones' => DateTimeZone::listIdentifiers(),
            'activeTab' => $showTenantSettingsRequest->activeTab(),
            'rotationEnabled' => $passwordRotationSettings->rotationEnabled,
            'maxAgeDays' => $passwordRotationSettings->maxAgeDays,
            'historyCount' => $passwordRotationSettings->historyCount,
            'minPasswordAgeDays' => PasswordRotationSettings::MIN_PASSWORD_AGE_DAYS,
            'maxPasswordAgeDays' => PasswordRotationSettings::MAX_PASSWORD_AGE_DAYS,
            'minHistoryCount' => PasswordRotationSettings::MIN_HISTORY_COUNT,
            'maxHistoryCount' => PasswordRotationSettings::MAX_HISTORY_COUNT,
            'twoFactorRequiredForAllUsers' => $twoFactorSettings->requiredForAllUsers,
            'twoFactorEmailOtpEnabled' => $twoFactorSettings->emailOtpEnabled,
            'twoFactorTotpEnabled' => $twoFactorSettings->totpEnabled,
        ]);
    }
}
