<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth\Sso;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\Sso\Contract\Query\BuildSsoRedirectInstructionQuery;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function redirect;
use function view;

#[SkipPermissionCheck('Guest SSO initiation; the IdP enforces auth from here.')]
final readonly class InitiateSsoLoginController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $slug): RedirectResponse|View
    {
        /** @var RedirectInstruction $redirectInstruction */
        $redirectInstruction = $this->queryBus->dispatch(new BuildSsoRedirectInstructionQuery($slug));

        if ($redirectInstruction->usesPostBinding) {
            return view('auth.sso.post-redirect', [
                'actionUrl' => $redirectInstruction->url,
                'fields' => $redirectInstruction->formFields,
            ]);
        }

        return redirect()->away($redirectInstruction->url);
    }
}
