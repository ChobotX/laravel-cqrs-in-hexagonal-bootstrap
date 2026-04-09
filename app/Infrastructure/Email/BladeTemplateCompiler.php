<?php

declare(strict_types=1);

namespace App\Infrastructure\Email;

use App\Contract\Tenancy\TenantContext;
use App\Domain\EmailTemplate\Contract\Service\TemplateCompiler;
use App\Domain\EmailTemplate\Contract\ValueObject\RenderedEmail;
use Illuminate\Contracts\View\Factory as ViewFactory;

final readonly class BladeTemplateCompiler implements TemplateCompiler
{
    private const string VARIABLE_PATTERN = '/\{\{\s*\$(\w+)\s*\}\}/';

    public function __construct(
        private ViewFactory $viewFactory,
        private TenantContext $tenantContext,
    ) {}

    /** @param array<string, string|null> $variables */
    public function compile(string $subjectTemplate, string $bodyTemplate, array $variables): RenderedEmail
    {
        $subject = $this->interpolate($subjectTemplate, $variables);
        $body = $this->interpolate($bodyTemplate, $variables);

        $htmlBody = $this->viewFactory->make('emails.layout', [
            'body' => $body,
            'tenantName' => $this->tenantContext->currentTenantName(),
            'tenantLogoUrl' => $this->tenantContext->currentTenantLogoUrl(),
        ])->render();

        return new RenderedEmail($subject, $htmlBody);
    }

    /**
     * Safe variable interpolation — only replaces {{ $varName }} placeholders.
     * Does NOT compile Blade directives (@php, @include, etc.) to prevent SSTI.
     *
     * @param  array<string, string|null>  $variables
     */
    private function interpolate(string $template, array $variables): string
    {
        return (string) preg_replace_callback(
            self::VARIABLE_PATTERN,
            static fn (array $match): string => $variables[$match[1]] ?? '',
            $template,
        );
    }
}
