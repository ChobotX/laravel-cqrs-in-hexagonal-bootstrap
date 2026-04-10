<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Command\ResetEmailTemplateCommand;

it('returns correct audit entity type', function (): void {
    $command = new ResetEmailTemplateCommand(templateType: 'user_invite', locale: 'en');

    expect($command->auditEntityType())->toBe('email_template');
});

it('returns type and locale as audit entity id', function (): void {
    $command = new ResetEmailTemplateCommand(templateType: 'user_invite', locale: 'en');

    expect($command->auditEntityId())->toBe('user_invite:en');
});

it('uses the locale from constructor in audit entity id', function (): void {
    $command = new ResetEmailTemplateCommand(templateType: 'password_reset', locale: 'cs');

    expect($command->auditEntityId())->toBe('password_reset:cs');
});
