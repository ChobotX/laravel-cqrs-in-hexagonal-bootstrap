<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\EmailTemplate\Constant\DefaultEmailTemplates;
use App\Domain\EmailTemplate\Contract\Command\ResetEmailTemplateCommand;
use App\Domain\EmailTemplate\Contract\Event\EmailTemplateReset;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;
use DateTimeImmutable;

/** @implements CommandHandler<ResetEmailTemplateCommand> */
final readonly class ResetEmailTemplateHandler implements CommandHandler
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->emailTemplateRepository->findByTypeAndLocale($command->templateType, $command->locale);

        if (! $existing instanceof \App\Domain\EmailTemplate\Contract\Entity\EmailTemplate) {
            throw new EmailTemplateNotFoundException($command->templateType, $command->locale);
        }

        $key = $command->templateType.':'.$command->locale;

        if (! array_key_exists($key, DefaultEmailTemplates::DEFAULTS)) {
            throw new EmailTemplateNotFoundException($command->templateType, $command->locale);
        }

        $default = DefaultEmailTemplates::DEFAULTS[$key];

        $this->emailTemplateRepository->updateContent(
            $command->templateType,
            $command->locale,
            $default['subject'],
            $default['body'],
        );

        $this->eventCollector->collect(new EmailTemplateReset(
            templateType: $command->templateType,
            locale: $command->locale,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
