<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Infrastructure\Eloquent\EmailTemplate\EmailTemplateMapper;
use App\Infrastructure\Eloquent\EmailTemplate\EmailTemplateModel;

it('maps email template model to domain entity', function (): void {
    EmailTemplateModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440aa3',
        'type' => 'user_invite',
        'locale' => 'fr',
        'subject_template' => 'Vous avez été invité à {{tenantName}}',
        'body_template' => '<p>Bienvenue {{userName}}</p>',
    ]);

    $model = EmailTemplateModel::find('550e8400-e29b-41d4-a716-446655440aa3');

    $mapper = new EmailTemplateMapper;
    $emailTemplate = $mapper->toDomain($model);

    expect($emailTemplate)->toBeInstanceOf(EmailTemplate::class)
        ->and($emailTemplate->id->value)->toBe('550e8400-e29b-41d4-a716-446655440aa3')
        ->and($emailTemplate->type->value)->toBe('user_invite')
        ->and($emailTemplate->locale->value)->toBe('fr')
        ->and($emailTemplate->subjectTemplate)->toBe('Vous avez été invité à {{tenantName}}')
        ->and($emailTemplate->bodyTemplate)->toBe('<p>Bienvenue {{userName}}</p>')
        ->and($emailTemplate->createdAt)->toBeInstanceOf(DateTimeImmutable::class)
        ->and($emailTemplate->updatedAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('maps different template types and locales', function (): void {
    $model = EmailTemplateModel::where('type', 'notification')->where('locale', 'cs')->first();

    $mapper = new EmailTemplateMapper;
    $emailTemplate = $mapper->toDomain($model);

    expect($emailTemplate->type->value)->toBe('notification')
        ->and($emailTemplate->locale->value)->toBe('cs');
});
