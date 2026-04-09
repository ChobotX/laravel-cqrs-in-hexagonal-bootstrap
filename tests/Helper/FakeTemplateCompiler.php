<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\EmailTemplate\Contract\Service\TemplateCompiler;
use App\Domain\EmailTemplate\Contract\ValueObject\RenderedEmail;

final class FakeTemplateCompiler implements TemplateCompiler
{
    /** @var list<array{subject: string, body: string, variables: array<string, string|null>}> */
    public array $calls = [];

    public function compile(string $subjectTemplate, string $bodyTemplate, array $variables): RenderedEmail
    {
        $this->calls[] = ['subject' => $subjectTemplate, 'body' => $bodyTemplate, 'variables' => $variables];

        $subject = $subjectTemplate;
        $body = $bodyTemplate;

        foreach ($variables as $name => $value) {
            $subject = str_replace('{{'.$name.'}}', $value ?? '', $subject);
            $body = str_replace('{{'.$name.'}}', $value ?? '', $body);
        }

        return new RenderedEmail($subject, '<html>'.$body.'</html>');
    }
}
