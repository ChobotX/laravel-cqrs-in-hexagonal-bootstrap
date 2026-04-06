<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Presentation\Http\Request\Auth\ShowAcceptInviteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

#[SkipPermissionCheck('Guest invite acceptance page')]
final readonly class ShowAcceptInviteController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(ShowAcceptInviteRequest $showAcceptInviteRequest, string $userId): View|RedirectResponse
    {
        $user = $this->queryBus->dispatch(new GetUserByIdQuery($userId));

        if ($user->isActivated) {
            return redirect('/login')->with('error', __('messages.exceptions.user_already_activated'));
        }

        return view('auth.accept-invite', ['userName' => $user->name->value]);
    }
}
