<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateWithMetadata;

function makeEmailTemplate(): EmailTemplate
{
    return new EmailTemplate(
        id: new EmailTemplateId('550e8400-e29b-41d4-a716-446655440000'),
        type: new EmailTemplateType('user_invite'),
        locale: new EmailTemplateLocale('en'),
        subjectTemplate: 'Hello {{userName}}',
        bodyTemplate: '<p>Welcome {{userName}}</p>',
        createdAt: new DateTimeImmutable('2025-01-01 00:00:00'),
        updatedAt: new DateTimeImmutable('2025-01-01 00:00:00'),
    );
}

it('holds template and type metadata', function (): void {
    $emailTemplate = makeEmailTemplate();
    $variables = [
        'userName' => ['description' => "Recipient's name", 'sensitive' => false, 'sample' => 'John Doe'],
        'link' => ['description' => 'Invite link', 'sensitive' => true, 'sample' => 'https://example.com/invite/abc'],
    ];

    $metadata = new EmailTemplateWithMetadata(
        template: $emailTemplate,
        typeName: 'User Invitation',
        typeDescription: 'Sent when a new user is invited',
        variables: $variables,
    );

    expect($metadata->template)->toBe($emailTemplate)
        ->and($metadata->typeName)->toBe('User Invitation')
        ->and($metadata->typeDescription)->toBe('Sent when a new user is invited')
        ->and($metadata->variables)->toBe($variables);
});

it('allows empty variables array', function (): void {
    $emailTemplate = makeEmailTemplate();

    $metadata = new EmailTemplateWithMetadata(
        template: $emailTemplate,
        typeName: 'Simple Type',
        typeDescription: 'No variables needed',
        variables: [],
    );

    expect($metadata->variables)->toBe([]);
});
