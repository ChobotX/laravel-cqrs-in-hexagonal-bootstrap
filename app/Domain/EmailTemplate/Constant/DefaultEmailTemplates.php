<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Constant;

final readonly class DefaultEmailTemplates
{
    /** @var array<string, array{subject: string, body: string}> keyed by "type:locale" */
    public const array DEFAULTS = [
        'user_invite:en' => [
            'subject' => 'You have been invited to {{ $tenantName }}',
            'body' => <<<'BLADE'
<h2>Welcome!</h2>
<p>You have been invited to join <strong>{{ $tenantName }}</strong>.</p>
<p>Hello {{ $userName }}, click the button below to set your password and activate your account:</p>
<p style="text-align: center; margin: 32px 0;">
    <a href="{{ $link }}" style="display: inline-block; padding: 12px 32px; background-color: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600;">Activate Account</a>
</p>
<p style="color: #6b7280; font-size: 14px;">This link expires in 72 hours. If you did not expect this invitation, you can safely ignore this email.</p>
BLADE,
        ],
        'user_invite:cs' => [
            'subject' => 'Byli jste pozváni do {{ $tenantName }}',
            'body' => <<<'BLADE'
<h2>Vítejte!</h2>
<p>Byli jste pozváni do organizace <strong>{{ $tenantName }}</strong>.</p>
<p>Dobrý den {{ $userName }}, klikněte na tlačítko níže pro nastavení hesla a aktivaci účtu:</p>
<p style="text-align: center; margin: 32px 0;">
    <a href="{{ $link }}" style="display: inline-block; padding: 12px 32px; background-color: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600;">Aktivovat účet</a>
</p>
<p style="color: #6b7280; font-size: 14px;">Tento odkaz vyprší za 72 hodin. Pokud jste tuto pozvánku neočekávali, můžete tento email ignorovat.</p>
BLADE,
        ],
        'password_reset:en' => [
            'subject' => 'Password Reset Request',
            'body' => <<<'BLADE'
<h2>Password Reset</h2>
<p>You have requested to reset your password for <strong>{{ $tenantName }}</strong>.</p>
<p>Click the button below to set a new password:</p>
<p style="text-align: center; margin: 32px 0;">
    <a href="{{ $link }}" style="display: inline-block; padding: 12px 32px; background-color: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600;">Reset Password</a>
</p>
<p style="color: #6b7280; font-size: 14px;">This link expires in 60 minutes. If you did not request this, you can safely ignore this email.</p>
BLADE,
        ],
        'password_reset:cs' => [
            'subject' => 'Žádost o obnovení hesla',
            'body' => <<<'BLADE'
<h2>Obnovení hesla</h2>
<p>Požádali jste o obnovení hesla pro <strong>{{ $tenantName }}</strong>.</p>
<p>Klikněte na tlačítko níže pro nastavení nového hesla:</p>
<p style="text-align: center; margin: 32px 0;">
    <a href="{{ $link }}" style="display: inline-block; padding: 12px 32px; background-color: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600;">Obnovit heslo</a>
</p>
<p style="color: #6b7280; font-size: 14px;">Tento odkaz vyprší za 60 minut. Pokud jste o obnovení hesla nežádali, můžete tento email ignorovat.</p>
BLADE,
        ],
        'two_factor_challenge:en' => [
            'subject' => 'Your verification code for {{ $tenantName }}',
            'body' => <<<'BLADE'
<h2>Verify your sign in</h2>
<p>Use this one-time code to finish signing in to <strong>{{ $tenantName }}</strong>:</p>
<p style="font-size: 28px; font-weight: 700; letter-spacing: 0.25em; text-align: center; margin: 24px 0;">{{ $code }}</p>
<p style="color: #6b7280; font-size: 14px;">This code expires in 10 minutes. If you did not request it, you can ignore this email.</p>
BLADE,
        ],
        'two_factor_challenge:cs' => [
            'subject' => 'Váš ověřovací kód pro {{ $tenantName }}',
            'body' => <<<'BLADE'
<h2>Ověření přihlášení</h2>
<p>Použijte tento jednorázový kód pro dokončení přihlášení do <strong>{{ $tenantName }}</strong>:</p>
<p style="font-size: 28px; font-weight: 700; letter-spacing: 0.25em; text-align: center; margin: 24px 0;">{{ $code }}</p>
<p style="color: #6b7280; font-size: 14px;">Kód vyprší za 10 minut. Pokud jste ho nevyžádali, tento email ignorujte.</p>
BLADE,
        ],
        'notification:en' => [
            'subject' => '{{ $title }}',
            'body' => <<<'BLADE'
<h2>{{ $title }}</h2>
<p>{{ $body }}</p>
<p style="text-align: center; margin: 32px 0;">
    <a href="{{ $link }}" style="display: inline-block; padding: 12px 32px; background-color: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600;">View Details</a>
</p>
BLADE,
        ],
        'notification:cs' => [
            'subject' => '{{ $title }}',
            'body' => <<<'BLADE'
<h2>{{ $title }}</h2>
<p>{{ $body }}</p>
<p style="text-align: center; margin: 32px 0;">
    <a href="{{ $link }}" style="display: inline-block; padding: 12px 32px; background-color: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600;">Zobrazit podrobnosti</a>
</p>
BLADE,
        ],
        'mail_test:en' => [
            'subject' => 'Email provider test from {{ $tenantName }}',
            'body' => <<<'BLADE'
<h2>It works.</h2>
<p>This is a test email sent from the <strong>{{ $tenantName }}</strong> email provider configuration.</p>
<p style="color: #6b7280; font-size: 14px;">If you can read this message in your inbox, your tenant's SMTP transport is delivering correctly.</p>
BLADE,
        ],
        'mail_test:cs' => [
            'subject' => 'Test poskytovatele e-mailu z {{ $tenantName }}',
            'body' => <<<'BLADE'
<h2>Funguje to.</h2>
<p>Toto je testovací e-mail odeslaný z konfigurace poskytovatele pro <strong>{{ $tenantName }}</strong>.</p>
<p style="color: #6b7280; font-size: 14px;">Pokud tuto zprávu vidíte ve své schránce, SMTP přenos vašeho nájemníka funguje správně.</p>
BLADE,
        ],
    ];
}
