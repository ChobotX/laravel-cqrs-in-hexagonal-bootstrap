<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Command\SeedDefaultEmailTemplatesCommand;
use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Service\DefaultEmailTemplateSeedData;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\EmailTemplate\Handler\Command\SeedDefaultEmailTemplatesHandler;
use Tests\Helper\FakeEmailTemplateRepository;
use Tests\Helper\FakeIdGenerator;

/** @param array<string, array{subject: string, body: string}> $templates */
function seedDataPort(array $templates): DefaultEmailTemplateSeedData
{
    return new readonly class($templates) implements DefaultEmailTemplateSeedData
    {
        /** @param array<string, array{subject: string, body: string}> $templates */
        public function __construct(private array $templates) {}

        public function templatesByTypeLocaleKey(): array
        {
            return $this->templates;
        }
    };
}

it('inserts each missing default template', function (): void {
    $repository = new FakeEmailTemplateRepository;
    $defaultEmailTemplateSeedData = seedDataPort([
        'user_invite:en' => ['subject' => 'Subject A', 'body' => 'Body A'],
        'password_reset:cs' => ['subject' => 'Předmět', 'body' => 'Tělo'],
    ]);
    $handler = new SeedDefaultEmailTemplatesHandler($repository, $defaultEmailTemplateSeedData, new FakeIdGenerator);

    $handler->handle(new SeedDefaultEmailTemplatesCommand);

    expect($repository->created)->toHaveCount(2)
        ->and($repository->created[0]->type->value)->toBe('user_invite')
        ->and($repository->created[0]->locale->value)->toBe('en')
        ->and($repository->created[0]->subjectTemplate)->toBe('Subject A')
        ->and($repository->created[1]->type->value)->toBe('password_reset')
        ->and($repository->created[1]->locale->value)->toBe('cs');
});

it('skips templates that already exist', function (): void {
    $existing = new EmailTemplate(
        id: new EmailTemplateId('11111111-2222-3333-4444-555555555555'),
        type: new EmailTemplateType('user_invite'),
        locale: new EmailTemplateLocale('en'),
        subjectTemplate: 'Existing subject',
        bodyTemplate: 'Existing body',
        createdAt: new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
        updatedAt: new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
    );
    $repository = new FakeEmailTemplateRepository(['user_invite:en' => $existing]);
    $defaultEmailTemplateSeedData = seedDataPort([
        'user_invite:en' => ['subject' => 'New subject', 'body' => 'New body'],
        'password_reset:en' => ['subject' => 'Reset', 'body' => 'Body'],
    ]);
    $handler = new SeedDefaultEmailTemplatesHandler($repository, $defaultEmailTemplateSeedData, new FakeIdGenerator);

    $handler->handle(new SeedDefaultEmailTemplatesCommand);

    expect($repository->created)->toHaveCount(1)
        ->and($repository->created[0]->type->value)->toBe('password_reset');
});
