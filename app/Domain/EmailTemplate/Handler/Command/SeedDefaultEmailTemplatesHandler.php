<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\IdGenerator;
use App\Domain\EmailTemplate\Contract\Command\SeedDefaultEmailTemplatesCommand;
use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Repository\EmailTemplateRepository;
use App\Domain\EmailTemplate\Contract\Service\DefaultEmailTemplateSeedData;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use DateTimeImmutable;

/** @implements CommandHandler<SeedDefaultEmailTemplatesCommand> */
#[SkipDomainEvent(reason: 'Idempotent bootstrap seeder — no domain state change beyond row inserts')]
final readonly class SeedDefaultEmailTemplatesHandler implements CommandHandler
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
        private DefaultEmailTemplateSeedData $defaultEmailTemplateSeedData,
        private IdGenerator $idGenerator,
    ) {}

    public function handle(Command $command): void
    {
        $now = new DateTimeImmutable;

        foreach ($this->defaultEmailTemplateSeedData->templatesByTypeLocaleKey() as $key => $template) {
            [$type, $locale] = explode(':', $key);

            if ($this->emailTemplateRepository->findByTypeAndLocale($type, $locale) instanceof EmailTemplate) {
                continue;
            }

            $this->emailTemplateRepository->create(new EmailTemplate(
                id: new EmailTemplateId($this->idGenerator->generate()),
                type: new EmailTemplateType($type),
                locale: new EmailTemplateLocale($locale),
                subjectTemplate: $template['subject'],
                bodyTemplate: $template['body'],
                createdAt: $now,
                updatedAt: $now,
            ));
        }
    }
}
