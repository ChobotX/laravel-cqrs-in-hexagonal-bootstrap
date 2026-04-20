<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sso;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Sso\Contract\Query\TestSsoConfigurationQuery;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;
use Illuminate\Http\RedirectResponse;

use function redirect;

#[RequiresPermission('sso.management.test')]
final readonly class TestSsoConfigurationController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $id): RedirectResponse
    {
        /** @var SsoConnectionTestResult $ssoConnectionTestResult */
        $ssoConnectionTestResult = $this->queryBus->dispatch(new TestSsoConfigurationQuery($id));

        return redirect()->route('settings.sso.index')
            ->with($ssoConnectionTestResult->success ? 'success' : 'error', $ssoConnectionTestResult->summary);
    }
}
