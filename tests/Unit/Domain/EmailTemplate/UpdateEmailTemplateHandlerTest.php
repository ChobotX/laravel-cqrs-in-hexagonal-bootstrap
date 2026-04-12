<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Application\Event\PropertyChangeBuilder;
use App\Domain\EmailTemplate\Constant\EmailTemplateFields;
use App\Domain\EmailTemplate\Contract\Command\UpdateEmailTemplateCommand;
use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\Event\EmailTemplateUpdated;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;
use App\Domain\EmailTemplate\Handler\Command\UpdateEmailTemplateHandler;
use Tests\Helper\FakeEmailTemplateRepository;
use Tests\Helper\FakeEventCollector;

function createEmailTemplate(string $type = 'user_invite', string $locale = 'en'): EmailTemplate
{
    return new EmailTemplate(
        id: new EmailTemplateId('550e8400-e29b-41d4-a716-446655440000'),
        type: new EmailTemplateType($type),
        locale: new EmailTemplateLocale($locale),
        subjectTemplate: 'Original subject',
        bodyTemplate: 'Original body',
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
        updatedAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    );
}

it('updates existing template content', function (): void {
    $emailTemplate = createEmailTemplate();
    $repository = new FakeEmailTemplateRepository(['user_invite:en' => $emailTemplate]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateEmailTemplateHandler($repository, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateEmailTemplateCommand(
        templateType: 'user_invite',
        locale: 'en',
        subjectTemplate: 'New subject',
        bodyTemplate: 'New body',
    ));

    expect($repository->updated)->toHaveKey('user_invite:en')
        ->and($repository->updated['user_invite:en']['subject'])->toBe('New subject')
        ->and($repository->updated['user_invite:en']['body'])->toBe('New body');
});

it('collects EmailTemplateUpdated event', function (): void {
    $emailTemplate = createEmailTemplate();
    $repository = new FakeEmailTemplateRepository(['user_invite:en' => $emailTemplate]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateEmailTemplateHandler($repository, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateEmailTemplateCommand(
        templateType: 'user_invite',
        locale: 'en',
        subjectTemplate: 'New subject',
        bodyTemplate: 'New body',
    ));

    expect($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(EmailTemplateUpdated::class);

    assert($eventCollector->collected[0] instanceof EmailTemplateUpdated);
    expect($eventCollector->collected[0]->templateType)->toBe('user_invite')
        ->and($eventCollector->collected[0]->locale)->toBe('en')
        ->and($eventCollector->collected[0]->changes())->toEqual([
            new PropertyChange(EmailTemplateFields::SUBJECT_TEMPLATE, 'Original subject', 'New subject'),
            new PropertyChange(EmailTemplateFields::BODY_TEMPLATE, 'Original body', 'New body'),
        ])
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('does not save or collect event when content is unchanged', function (): void {
    $emailTemplate = createEmailTemplate();
    $repository = new FakeEmailTemplateRepository(['user_invite:en' => $emailTemplate]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateEmailTemplateHandler($repository, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateEmailTemplateCommand(
        templateType: 'user_invite',
        locale: 'en',
        subjectTemplate: 'Original subject',
        bodyTemplate: 'Original body',
    ));

    expect($repository->updated)->toBeEmpty()
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('throws EmailTemplateNotFoundException for non-existent template', function (): void {
    $repository = new FakeEmailTemplateRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateEmailTemplateHandler($repository, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateEmailTemplateCommand(
        templateType: 'nonexistent',
        locale: 'en',
        subjectTemplate: 'Subject',
        bodyTemplate: 'Body',
    ));
})->throws(EmailTemplateNotFoundException::class);
