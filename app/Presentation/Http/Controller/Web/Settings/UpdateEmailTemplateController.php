<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Domain\EmailTemplate\Contract\Command\UpdateEmailTemplateCommand;
use App\Presentation\Http\Request\Web\Settings\UpdateEmailTemplateRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('email_templates.templates.update')]
final readonly class UpdateEmailTemplateController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateEmailTemplateRequest $updateEmailTemplateRequest, string $type, string $locale): RedirectResponse
    {
        $this->commandBus->dispatch(new UpdateEmailTemplateCommand(
            templateType: $type,
            locale: $locale,
            subjectTemplate: $updateEmailTemplateRequest->string('subject_template')->toString(),
            bodyTemplate: $updateEmailTemplateRequest->string('body_template')->toString(),
        ));

        return redirect()->route('settings.email-templates.index')
            ->with('success', __('messages.email_templates.updated'));
    }
}
