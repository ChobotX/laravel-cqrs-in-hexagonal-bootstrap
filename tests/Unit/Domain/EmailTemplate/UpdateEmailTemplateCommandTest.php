<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Command\UpdateEmailTemplateCommand;

it('returns correct audit entity type', function (): void {
    $command = new UpdateEmailTemplateCommand(
        templateType: 'user_invite',
        locale: 'en',
        subjectTemplate: 'Hello',
        bodyTemplate: '<p>Body</p>',
    );

    expect($command->auditEntityType())->toBe('email_template');
});

it('returns type and locale as audit entity id', function (): void {
    $command = new UpdateEmailTemplateCommand(
        templateType: 'user_invite',
        locale: 'en',
        subjectTemplate: 'Hello',
        bodyTemplate: '<p>Body</p>',
    );

    expect($command->auditEntityId())->toBe('user_invite:en');
});

it('uses the locale from constructor in audit entity id', function (): void {
    $command = new UpdateEmailTemplateCommand(
        templateType: 'password_reset',
        locale: 'cs',
        subjectTemplate: 'Reset',
        bodyTemplate: '<p>Click here</p>',
    );

    expect($command->auditEntityId())->toBe('password_reset:cs');
});
