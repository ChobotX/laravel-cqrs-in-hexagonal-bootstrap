<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Sso;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Query\ListSsoConfigurationsQuery;
use Illuminate\View\View;

use function view;

#[RequiresPermission('sso.management.read')]
final readonly class ListSsoConfigurationsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        /** @var list<SsoConfiguration> $configurations */
        $configurations = $this->queryBus->dispatch(new ListSsoConfigurationsQuery);

        return view('sso.index', ['configurations' => $configurations]);
    }
}
