<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Constant\DefaultEmailTemplates;
use App\Domain\EmailTemplate\Contract\Command\ResetEmailTemplateCommand;
use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Event\EmailTemplateReset;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;
use App\Domain\EmailTemplate\Handler\Command\ResetEmailTemplateHandler;
use Tests\Helper\FakeEmailTemplateRepository;
use Tests\Helper\FakeEventCollector;

function createCustomizedEmailTemplate(string $type = 'user_invite', string $locale = 'en'): EmailTemplate
{
    return new EmailTemplate(
        id: new EmailTemplateId('550e8400-e29b-41d4-a716-446655440000'),
        type: new EmailTemplateType($type),
        locale: new EmailTemplateLocale($locale),
        subjectTemplate: 'Customized subject',
        bodyTemplate: 'Customized body',
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
        updatedAt: new DateTimeImmutable('2026-01-20T10:00:00+00:00'),
    );
}

it('resets template to default content', function (): void {
    $template = createCustomizedEmailTemplate();
    $repository = new FakeEmailTemplateRepository(['user_invite:en' => $template]);
    $eventCollector = new FakeEventCollector;

    $handler = new ResetEmailTemplateHandler($repository, $eventCollector);

    $handler->handle(new ResetEmailTemplateCommand(
        templateType: 'user_invite',
        locale: 'en',
    ));

    $default = DefaultEmailTemplates::DEFAULTS['user_invite:en'];

    expect($repository->updated)->toHaveKey('user_invite:en')
        ->and($repository->updated['user_invite:en']['subject'])->toBe($default['subject'])
        ->and($repository->updated['user_invite:en']['body'])->toBe($default['body']);
});

it('collects EmailTemplateReset event', function (): void {
    $template = createCustomizedEmailTemplate();
    $repository = new FakeEmailTemplateRepository(['user_invite:en' => $template]);
    $eventCollector = new FakeEventCollector;

    $handler = new ResetEmailTemplateHandler($repository, $eventCollector);

    $handler->handle(new ResetEmailTemplateCommand(
        templateType: 'user_invite',
        locale: 'en',
    ));

    expect($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(EmailTemplateReset::class);

    assert($eventCollector->collected[0] instanceof EmailTemplateReset);
    expect($eventCollector->collected[0]->templateType)->toBe('user_invite')
        ->and($eventCollector->collected[0]->locale)->toBe('en')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws EmailTemplateNotFoundException when template does not exist in repository', function (): void {
    $repository = new FakeEmailTemplateRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new ResetEmailTemplateHandler($repository, $eventCollector);

    $handler->handle(new ResetEmailTemplateCommand(
        templateType: 'user_invite',
        locale: 'en',
    ));
})->throws(EmailTemplateNotFoundException::class);

it('throws EmailTemplateNotFoundException when no default exists for type and locale', function (): void {
    $template = new EmailTemplate(
        id: new EmailTemplateId('550e8400-e29b-41d4-a716-446655440000'),
        type: new EmailTemplateType('custom_type'),
        locale: new EmailTemplateLocale('en'),
        subjectTemplate: 'Subject',
        bodyTemplate: 'Body',
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
        updatedAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    );
    $repository = new FakeEmailTemplateRepository(['custom_type:en' => $template]);
    $eventCollector = new FakeEventCollector;

    $handler = new ResetEmailTemplateHandler($repository, $eventCollector);

    $handler->handle(new ResetEmailTemplateCommand(
        templateType: 'custom_type',
        locale: 'en',
    ));
})->throws(EmailTemplateNotFoundException::class);
