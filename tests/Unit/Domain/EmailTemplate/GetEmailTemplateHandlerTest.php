<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Constant\EmailTemplateTypes;
use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Query\GetEmailTemplateQuery;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateWithMetadata;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;
use App\Domain\EmailTemplate\Handler\Query\GetEmailTemplateHandler;
use Tests\Helper\FakeEmailTemplateRepository;

function makeTemplate(string $type = 'user_invite', string $locale = 'en'): EmailTemplate
{
    return new EmailTemplate(
        id: new EmailTemplateId('550e8400-e29b-41d4-a716-446655440000'),
        type: new EmailTemplateType($type),
        locale: new EmailTemplateLocale($locale),
        subjectTemplate: 'Subject {{userName}}',
        bodyTemplate: '<p>Body {{userName}}</p>',
        createdAt: new DateTimeImmutable('2025-01-01 00:00:00'),
        updatedAt: new DateTimeImmutable('2025-01-01 00:00:00'),
    );
}

it('returns template wrapped in metadata for a known type', function (): void {
    $emailTemplate = makeTemplate('user_invite', 'en');
    $repository = new FakeEmailTemplateRepository(['user_invite:en' => $emailTemplate]);
    $handler = new GetEmailTemplateHandler($repository);

    $emailTemplateWithMetadata = $handler->handle(new GetEmailTemplateQuery('user_invite', 'en'));

    $typeConfig = EmailTemplateTypes::TYPES['user_invite'];

    expect($emailTemplateWithMetadata)->toBeInstanceOf(EmailTemplateWithMetadata::class)
        ->and($emailTemplateWithMetadata->template)->toBe($emailTemplate)
        ->and($emailTemplateWithMetadata->typeName)->toBe($typeConfig['name'])
        ->and($emailTemplateWithMetadata->typeDescription)->toBe($typeConfig['description'])
        ->and($emailTemplateWithMetadata->variables)->toBe($typeConfig['variables']);
});

it('uses template type as name when type is not in EmailTemplateTypes', function (): void {
    $emailTemplate = makeTemplate('custom_type', 'en');
    $repository = new FakeEmailTemplateRepository(['custom_type:en' => $emailTemplate]);
    $handler = new GetEmailTemplateHandler($repository);

    $emailTemplateWithMetadata = $handler->handle(new GetEmailTemplateQuery('custom_type', 'en'));

    expect($emailTemplateWithMetadata->typeName)->toBe('custom_type')
        ->and($emailTemplateWithMetadata->typeDescription)->toBe('')
        ->and($emailTemplateWithMetadata->variables)->toBe([]);
});

it('throws when template is not found in repository', function (): void {
    $repository = new FakeEmailTemplateRepository;
    $handler = new GetEmailTemplateHandler($repository);

    $handler->handle(new GetEmailTemplateQuery('user_invite', 'en'));
})->throws(EmailTemplateNotFoundException::class);
