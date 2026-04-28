<?php

declare(strict_types=1);

use App\Contract\Bus\CommandBus;
use App\Domain\EmailTemplate\Contract\Service\EmailSender;
use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Domain\Tenancy\Contract\Repository\TenantMailTransportRepository;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Presentation\Http\Controller\Web\Settings\ShowMailSettingsController;
use App\Presentation\Http\Controller\Web\Settings\UpdateMailSettingsController;
use App\Presentation\Http\Request\Web\Settings\UpdateMailSettingsRequest;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Helper\FakeEmailSender;

function mailSettingsWebUser(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f30',
        'name' => 'Mail Admin',
        'email' => 'mail-admin@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

beforeEach(function (): void {
    DB::connection('tenant')->table('tenant_mail_settings')->truncate();
});

it('shows mail settings page', function (): void {
    $userModel = mailSettingsWebUser();

    $this->actingAs($userModel)
        ->get(route('settings.index', ['tab' => 'mail']))
        ->assertOk()
        ->assertSee(__('messages.settings.mail_title'))
        ->assertSee('data-testid="mail-settings-title"', escape: false);
});

it('saves a custom mail transport', function (): void {
    $userModel = mailSettingsWebUser();

    $this->actingAs($userModel)
        ->put(route('settings.mail.update'), [
            'use_custom' => '1',
            'provider' => MailProvider::Mailjet->value,
            'host' => 'in-v3.mailjet.com',
            'port' => '587',
            'username' => 'apiuser',
            'password' => 'apisecret',
            'encryption' => 'tls',
            'from_address' => 'team@acme.com',
            'from_name' => 'Acme',
        ])
        ->assertRedirect(route('settings.index', ['tab' => 'mail']));

    $tenantMailTransportRepository = app(TenantMailTransportRepository::class);
    $stored = $tenantMailTransportRepository->findCustom();

    expect($stored)->not->toBeNull()
        ->and($stored?->host)->toBe('in-v3.mailjet.com')
        ->and($stored?->port)->toBe(587)
        ->and($stored?->password)->toBe('apisecret')
        ->and($stored?->fromAddress)->toBe('team@acme.com');
});

it('clears the override when use_custom is false', function (): void {
    $userModel = mailSettingsWebUser();

    app(TenantMailTransportRepository::class)->save(new MailTransport(
        provider: MailProvider::Mailpit,
        host: 'mailpit',
        port: 1025,
        username: null,
        password: null,
        encryption: null,
        fromAddress: 'a@b.c',
        fromName: 'A',
        isCustom: true,
    ));

    $this->actingAs($userModel)
        ->put(route('settings.mail.update'), [
            'use_custom' => '0',
            'from_address' => 'still@valid.com',
            'from_name' => 'Whatever',
            'host' => 'ignored',
            'port' => '25',
            'provider' => MailProvider::Custom->value,
        ])
        ->assertRedirect(route('settings.index', ['tab' => 'mail']));

    expect(app(TenantMailTransportRepository::class)->findCustom())->toBeNull();
});

it('rejects invalid email when saving custom transport', function (): void {
    $userModel = mailSettingsWebUser();

    $this->actingAs($userModel)
        ->put(route('settings.mail.update'), [
            'use_custom' => '1',
            'provider' => MailProvider::Custom->value,
            'host' => 'smtp.example.com',
            'port' => '587',
            'from_address' => 'not-an-email',
            'from_name' => 'Acme',
        ])
        ->assertSessionHasErrors('from_address');
});

it('requires host when use_custom is true', function (): void {
    $userModel = mailSettingsWebUser();

    $this->actingAs($userModel)
        ->put(route('settings.mail.update'), [
            'use_custom' => '1',
            'provider' => MailProvider::Custom->value,
            'port' => '587',
            'from_address' => 'a@b.c',
            'from_name' => 'A',
        ])
        ->assertSessionHasErrors('host');
});

it('show redirects to mail settings tab', function (): void {
    $controller = new ShowMailSettingsController;

    $redirectResponse = $controller();

    expect($redirectResponse->getTargetUrl())->toBe(route('settings.index', ['tab' => 'mail']));
});

it('update aborts when tenant_id is missing from context', function (): void {
    Context::flush();
    $mock = Mockery::mock(CommandBus::class);
    $controller = new UpdateMailSettingsController($mock);

    $updateMailSettingsRequest = UpdateMailSettingsRequest::create(
        '/settings/mail',
        'PUT',
        [
            'use_custom' => '0',
        ],
    );

    $controller($updateMailSettingsRequest);
})->throws(Symfony\Component\HttpKernel\Exception\HttpException::class);

it('requires settings.tenant.update to save mail settings', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f31',
        'name' => 'Read Only Mail',
        'email' => 'readonly-mail@test.com',
        'password' => Hash::make('password'),
    ]);

    $role = test()->seedRoleWithPermissions('Read mail settings', 'Read', ['settings.tenant.read' => 'all']);
    test()->assignRole($user->id, $role->id);

    $this->actingAs($user)
        ->put(route('settings.mail.update'), [
            'use_custom' => '0',
        ])
        ->assertForbidden();
});

it('sends a test email through the configured transport', function (): void {
    $userModel = mailSettingsWebUser();
    $emailSender = new FakeEmailSender;
    $this->app->instance(EmailSender::class, $emailSender);

    $this->actingAs($userModel)
        ->post(route('settings.mail.test'))
        ->assertRedirect(route('settings.index', ['tab' => 'mail']))
        ->assertSessionHas('success');

    expect($emailSender->sent)->toHaveCount(1)
        ->and($emailSender->sent[0]['recipientEmail'])->toBe('mail-admin@test.com')
        ->and($emailSender->sent[0]['transport']->isCustom)->toBeFalse();
});

it('flashes an error when the test email transport fails', function (): void {
    $userModel = mailSettingsWebUser();
    $emailSender = new class implements EmailSender
    {
        public function sendHtml(MailTransport $mailTransport, string $recipientEmail, string $subject, string $htmlBody): void
        {
            throw new RuntimeException('connection refused');
        }
    };
    $this->app->instance(EmailSender::class, $emailSender);

    $this->actingAs($userModel)
        ->post(route('settings.mail.test'))
        ->assertRedirect(route('settings.index', ['tab' => 'mail']))
        ->assertSessionHas('error');
});

it('forbids sending a test email without settings.tenant.update', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f32',
        'name' => 'Read Only Test',
        'email' => 'readonly-test-mail@test.com',
        'password' => Hash::make('password'),
    ]);

    $role = test()->seedRoleWithPermissions('Read mail test', 'Read', ['settings.tenant.read' => 'all']);
    test()->assignRole($user->id, $role->id);

    $this->actingAs($user)
        ->post(route('settings.mail.test'))
        ->assertForbidden();
});
