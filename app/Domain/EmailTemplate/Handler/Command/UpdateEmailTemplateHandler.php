<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Command;

use App\Application\Event\PropertyChange;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\EmailTemplate\Constant\EmailTemplateFields;
use App\Domain\EmailTemplate\Contract\Command\UpdateEmailTemplateCommand;
use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Event\EmailTemplateUpdated;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateEmailTemplateCommand> */
final readonly class UpdateEmailTemplateHandler implements CommandHandler
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->emailTemplateRepository->findByTypeAndLocale($command->templateType, $command->locale);

        if (! $existing instanceof EmailTemplate) {
            throw new EmailTemplateNotFoundException($command->templateType, $command->locale);
        }

        $changes = $this->buildChanges($existing, $command);

        if ($changes === []) {
            return;
        }

        $this->emailTemplateRepository->updateContent(
            $command->templateType,
            $command->locale,
            $command->subjectTemplate,
            $command->bodyTemplate,
        );

        $this->eventCollector->collect(new EmailTemplateUpdated(
            templateType: $command->templateType,
            locale: $command->locale,
            changes: $changes,
            occurredAt: new DateTimeImmutable,
        ));
    }

    /** @return list<PropertyChange> */
    private function buildChanges(EmailTemplate $emailTemplate, UpdateEmailTemplateCommand $updateEmailTemplateCommand): array
    {
        $changes = [];

        if ($emailTemplate->subjectTemplate !== $updateEmailTemplateCommand->subjectTemplate) {
            $changes[] = new PropertyChange(EmailTemplateFields::SUBJECT_TEMPLATE, $emailTemplate->subjectTemplate, $updateEmailTemplateCommand->subjectTemplate);
        }

        if ($emailTemplate->bodyTemplate !== $updateEmailTemplateCommand->bodyTemplate) {
            $changes[] = new PropertyChange(EmailTemplateFields::BODY_TEMPLATE, $emailTemplate->bodyTemplate, $updateEmailTemplateCommand->bodyTemplate);
        }

        return $changes;
    }
}
