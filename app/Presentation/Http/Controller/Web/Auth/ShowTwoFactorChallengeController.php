<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\User\Contract\Query\GetTwoFactorStatusQuery;
use Illuminate\View\View;

#[SkipPermissionCheck('Authenticated users can view two-factor challenge')]
final readonly class ShowTwoFactorChallengeController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        return view('auth.two-factor-challenge', [
            'status' => $this->queryBus->dispatch(new GetTwoFactorStatusQuery),
        ]);
    }
}
