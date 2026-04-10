<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Infrastructure\Eloquent\EmailTemplate\EloquentEmailTemplateRepository;
use App\Infrastructure\Eloquent\EmailTemplate\EmailTemplateMapper;
use App\Infrastructure\Eloquent\EmailTemplate\EmailTemplateModel;

function makeEloquentTemplateRepository(): EloquentEmailTemplateRepository
{
    return new EloquentEmailTemplateRepository(new EmailTemplateMapper);
}

function makeEloquentEmailTemplate(
    string $id,
    string $type,
    string $locale,
    string $subject = 'Subject {{name}}',
    string $body = '<p>Body {{name}}</p>',
): EmailTemplate {
    return new EmailTemplate(
        new EmailTemplateId($id),
        new EmailTemplateType($type),
        new EmailTemplateLocale($locale),
        $subject,
        $body,
        new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
        new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
    );
}

// ─── findByTypeAndLocale ────────────────────────────────────────────────────

it('finds template by type and locale', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();

    $template = $eloquentEmailTemplateRepository->findByTypeAndLocale('user_invite', 'en');

    expect($template)->toBeInstanceOf(EmailTemplate::class)
        ->and($template->type->value)->toBe('user_invite')
        ->and($template->locale->value)->toBe('en');
});

it('returns null when template not found by type and locale', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();

    $result = $eloquentEmailTemplateRepository->findByTypeAndLocale('nonexistent_type', 'en');

    expect($result)->toBeNull();
});

// ─── findAllByType ──────────────────────────────────────────────────────────

it('returns all templates of a given type', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();

    $templates = $eloquentEmailTemplateRepository->findAllByType('user_invite');

    expect($templates)->toBeArray()
        ->and(count($templates))->toBeGreaterThanOrEqual(2); // seeded en + cs
    foreach ($templates as $template) {
        expect($template->type->value)->toBe('user_invite');
    }
});

it('returns empty list for type with no templates', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();

    $results = $eloquentEmailTemplateRepository->findAllByType('no_such_type');

    expect($results)->toBe([]);
});

// ─── findAll ────────────────────────────────────────────────────────────────

it('returns all templates', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();

    $templates = $eloquentEmailTemplateRepository->findAll();

    expect($templates)->toBeArray()
        ->and(count($templates))->toBeGreaterThanOrEqual(6); // seeder seeds 6 defaults
    foreach ($templates as $template) {
        expect($template)->toBeInstanceOf(EmailTemplate::class);
    }
});

// ─── create ─────────────────────────────────────────────────────────────────

it('creates a new email template', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();
    $emailTemplate = makeEloquentEmailTemplate(
        '550e8400-e29b-41d4-a716-446655440aa1',
        'user_invite',
        'fr',
        'Bienvenue {{userName}}',
        '<p>Bonjour {{userName}}</p>',
    );

    $eloquentEmailTemplateRepository->create($emailTemplate);

    $this->assertDatabaseHas('email_templates', [
        'id' => '550e8400-e29b-41d4-a716-446655440aa1',
        'type' => 'user_invite',
        'locale' => 'fr',
        'subject_template' => 'Bienvenue {{userName}}',
        'body_template' => '<p>Bonjour {{userName}}</p>',
    ]);
});

// ─── updateContent ──────────────────────────────────────────────────────────

it('updates subject and body of an existing template', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();

    $eloquentEmailTemplateRepository->updateContent('user_invite', 'en', 'New Subject', '<p>New Body</p>');

    $this->assertDatabaseHas('email_templates', [
        'type' => 'user_invite',
        'locale' => 'en',
        'subject_template' => 'New Subject',
        'body_template' => '<p>New Body</p>',
    ]);
});

it('does nothing when updating a nonexistent template', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();

    // Should not throw
    $eloquentEmailTemplateRepository->updateContent('nonexistent_type', 'en', 'Subject', 'Body');

    expect(true)->toBeTrue();
});

// ─── deleteByTypeAndLocale ──────────────────────────────────────────────────

it('deletes template by type and locale', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();

    // Create a template to delete so we don't remove seeded data
    EmailTemplateModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440aa2',
        'type' => 'user_invite',
        'locale' => 'de',
        'subject_template' => 'Subject DE',
        'body_template' => 'Body DE',
    ]);

    $eloquentEmailTemplateRepository->deleteByTypeAndLocale('user_invite', 'de');

    $this->assertDatabaseMissing('email_templates', [
        'type' => 'user_invite',
        'locale' => 'de',
    ]);
});

it('does not throw when deleting a nonexistent template', function (): void {
    $eloquentEmailTemplateRepository = makeEloquentTemplateRepository();

    $eloquentEmailTemplateRepository->deleteByTypeAndLocale('user_invite', 'xx');

    expect(true)->toBeTrue();
});
