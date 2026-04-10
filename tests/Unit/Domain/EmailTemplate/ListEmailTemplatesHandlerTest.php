<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Constant\EmailTemplateTypes;
use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Query\ListEmailTemplatesQuery;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\EmailTemplate\Handler\Query\ListEmailTemplatesHandler;
use Tests\Helper\FakeEmailTemplateRepository;

function makeListTemplate(string $type, string $locale, string $updatedAt = '2025-06-01 00:00:00'): EmailTemplate
{
    return new EmailTemplate(
        id: new EmailTemplateId('550e8400-e29b-41d4-a716-446655440000'),
        type: new EmailTemplateType($type),
        locale: new EmailTemplateLocale($locale),
        subjectTemplate: 'Subject',
        bodyTemplate: 'Body',
        createdAt: new DateTimeImmutable('2025-01-01 00:00:00'),
        updatedAt: new DateTimeImmutable($updatedAt),
    );
}

it('returns all types from EmailTemplateTypes with grouped locales', function (): void {
    $emailTemplate = makeListTemplate('user_invite', 'en', '2025-06-01 00:00:00');
    $csTemplate = makeListTemplate('user_invite', 'cs', '2025-06-15 00:00:00');
    $repository = new FakeEmailTemplateRepository([
        'user_invite:en' => $emailTemplate,
        'user_invite:cs' => $csTemplate,
    ]);
    $handler = new ListEmailTemplatesHandler($repository);

    $result = $handler->handle(new ListEmailTemplatesQuery);

    expect($result)->toHaveCount(count(EmailTemplateTypes::TYPES));

    /** @var array{type: string, name: string, description: string, locales: list<string>, updatedAt: string|null} $userInviteEntry */
    $userInviteEntry = collect($result)->firstWhere('type', 'user_invite');

    expect($userInviteEntry)->not->toBeNull()
        ->and($userInviteEntry['name'])->toBe(EmailTemplateTypes::TYPES['user_invite']['name'])
        ->and($userInviteEntry['description'])->toBe(EmailTemplateTypes::TYPES['user_invite']['description'])
        ->and($userInviteEntry['locales'])->toContain('en')
        ->and($userInviteEntry['locales'])->toContain('cs');
});

it('reports latest updatedAt across locales', function (): void {
    $emailTemplate = makeListTemplate('user_invite', 'en', '2025-06-01 00:00:00');
    $csTemplate = makeListTemplate('user_invite', 'cs', '2025-06-15 00:00:00');
    $repository = new FakeEmailTemplateRepository([
        'user_invite:en' => $emailTemplate,
        'user_invite:cs' => $csTemplate,
    ]);
    $handler = new ListEmailTemplatesHandler($repository);

    $result = $handler->handle(new ListEmailTemplatesQuery);

    /** @var array{type: string, name: string, description: string, locales: list<string>, updatedAt: string|null} $userInviteEntry */
    $userInviteEntry = collect($result)->firstWhere('type', 'user_invite');

    expect($userInviteEntry['updatedAt'])->toContain('2025-06-15');
});

it('returns empty locales and null updatedAt for types with no stored templates', function (): void {
    $repository = new FakeEmailTemplateRepository;
    $handler = new ListEmailTemplatesHandler($repository);

    $result = $handler->handle(new ListEmailTemplatesQuery);

    expect($result)->toHaveCount(count(EmailTemplateTypes::TYPES));

    foreach ($result as $entry) {
        expect($entry['locales'])->toBe([])
            ->and($entry['updatedAt'])->toBeNull();
    }
});
