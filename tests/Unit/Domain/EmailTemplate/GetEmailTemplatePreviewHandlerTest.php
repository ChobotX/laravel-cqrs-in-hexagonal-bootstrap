<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Constant\EmailTemplateTypes;
use App\Domain\EmailTemplate\Contract\Query\GetEmailTemplatePreviewQuery;
use App\Domain\EmailTemplate\Contract\ValueObject\RenderedEmail;
use App\Domain\EmailTemplate\Handler\Query\GetEmailTemplatePreviewHandler;
use Tests\Helper\FakeTemplateCompiler;

it('compiles template with sample variables for a known type', function (): void {
    $compiler = new FakeTemplateCompiler;
    $handler = new GetEmailTemplatePreviewHandler($compiler);

    $renderedEmail = $handler->handle(new GetEmailTemplatePreviewQuery(
        templateType: 'user_invite',
        locale: 'en',
        subjectTemplate: 'Hello {{userName}}',
        bodyTemplate: '<p>Welcome {{userName}}, join via {{link}}</p>',
    ));

    $expectedVariables = array_map(
        static fn (array $config): string => $config['sample'],
        EmailTemplateTypes::TYPES['user_invite']['variables'],
    );

    expect($renderedEmail)->toBeInstanceOf(RenderedEmail::class)
        ->and($compiler->calls)->toHaveCount(1)
        ->and($compiler->calls[0]['subject'])->toBe('Hello {{userName}}')
        ->and($compiler->calls[0]['body'])->toBe('<p>Welcome {{userName}}, join via {{link}}</p>')
        ->and($compiler->calls[0]['variables'])->toBe($expectedVariables);
});

it('compiles with empty variables when template type is unknown', function (): void {
    $compiler = new FakeTemplateCompiler;
    $handler = new GetEmailTemplatePreviewHandler($compiler);

    $renderedEmail = $handler->handle(new GetEmailTemplatePreviewQuery(
        templateType: 'unknown_type',
        locale: 'en',
        subjectTemplate: 'Subject',
        bodyTemplate: 'Body',
    ));

    expect($renderedEmail)->toBeInstanceOf(RenderedEmail::class)
        ->and($compiler->calls)->toHaveCount(1)
        ->and($compiler->calls[0]['variables'])->toBe([]);
});
