<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Presentation\Http\Request\Web\Settings\ShowTenantSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Throwable;

#[RequiresPermission('settings.tenant.update')]
final readonly class SendTestMailController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(): RedirectResponse
    {
        $redirectResponse = redirect()->route('settings.index', ['tab' => ShowTenantSettingsRequest::MAIL_TAB]);

        try {
            $this->commandBus->dispatch(new SendTemplatedEmailCommand(
                userId: (string) Auth::id(),
                templateType: 'mail_test',
                locale: App::getLocale(),
                variables: [],
            ));
        } catch (Throwable $throwable) {
            // @silent: bus pipeline already logs the failure; surface it as a UI flash
            return $redirectResponse->with('error', __('messages.settings.mail_test_failed', ['error' => $throwable->getMessage()]));
        }

        return $redirectResponse->with('success', __('messages.settings.mail_test_sent'));
    }
}
