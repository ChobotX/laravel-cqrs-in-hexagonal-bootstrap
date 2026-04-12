<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Command;

use App\Application\Event\PropertyChangeBuilder;
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
        private PropertyChangeBuilder $propertyChangeBuilder,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->emailTemplateRepository->findByTypeAndLocale($command->templateType, $command->locale);

        if (! $existing instanceof EmailTemplate) {
            throw new EmailTemplateNotFoundException($command->templateType, $command->locale);
        }

        $changes = $this->propertyChangeBuilder->diff([
            EmailTemplateFields::SUBJECT_TEMPLATE => [$existing->subjectTemplate, $command->subjectTemplate],
            EmailTemplateFields::BODY_TEMPLATE => [$existing->bodyTemplate, $command->bodyTemplate],
        ]);

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
}
