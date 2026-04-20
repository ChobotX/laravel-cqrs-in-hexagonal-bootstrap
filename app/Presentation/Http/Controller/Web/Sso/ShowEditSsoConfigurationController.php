<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sso;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Query\GetSsoConfigurationByIdQuery;
use Illuminate\View\View;

use function view;

#[RequiresPermission('sso.management.update')]
final readonly class ShowEditSsoConfigurationController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $id): View
    {
        /** @var SsoConfiguration $configuration */
        $configuration = $this->queryBus->dispatch(new GetSsoConfigurationByIdQuery($id));

        return view('sso.edit', [
            'configuration' => $configuration,
            'jitModes' => JitMode::cases(),
        ]);
    }
}
