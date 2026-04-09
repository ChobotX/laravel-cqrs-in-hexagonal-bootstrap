<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\EmailTemplate\Contract\Command\ResetEmailTemplateCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('email_templates.templates.update')]
final readonly class ResetEmailTemplateController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $type, string $locale): RedirectResponse
    {
        $this->commandBus->dispatch(new ResetEmailTemplateCommand(
            templateType: $type,
            locale: $locale,
        ));

        return redirect()->route('settings.email-templates.index')
            ->with('success', __('messages.email_templates.reset'));
    }
}
