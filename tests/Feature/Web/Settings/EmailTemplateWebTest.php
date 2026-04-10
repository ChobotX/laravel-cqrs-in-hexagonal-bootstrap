<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\EmailTemplate\EmailLogModel;
use App\Infrastructure\Eloquent\EmailTemplate\EmailTemplateModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

// ─── ListEmailTemplatesController ──────────────────────────────────────────

it('shows email templates index for user with permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441000',
        'name' => 'Template Admin',
        'email' => 'template-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/settings/email-templates')
        ->assertOk()
        ->assertViewIs('settings.email-templates.index');
});

it('returns 403 on email templates index without permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441001',
        'name' => 'No Perm User',
        'email' => 'no-perm-templates@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/settings/email-templates')
        ->assertForbidden();
});

it('redirects unauthenticated user to login from email templates index', function (): void {
    $this->get('/settings/email-templates')
        ->assertRedirect('/login?'.http_build_query(['redirect' => '/settings/email-templates']));
});

it('lists seeded email templates on index page', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441002',
        'name' => 'Template Viewer',
        'email' => 'template-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/settings/email-templates')
        ->assertOk()
        ->assertSee('user_invite');
});

// ─── EditEmailTemplateController ───────────────────────────────────────────

it('shows edit page for existing email template', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441010',
        'name' => 'Editor Admin',
        'email' => 'editor-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/settings/email-templates/user_invite/en')
        ->assertOk()
        ->assertViewIs('settings.email-templates.edit');
});

it('returns 403 on edit page without permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441011',
        'name' => 'No Edit Perm',
        'email' => 'no-edit-perm@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/settings/email-templates/user_invite/en')
        ->assertForbidden();
});

it('redirects unauthenticated user to login from edit page', function (): void {
    $this->get('/settings/email-templates/user_invite/en')
        ->assertRedirect('/login?'.http_build_query(['redirect' => '/settings/email-templates/user_invite/en']));
});

it('returns 404 for nonexistent template type on edit page', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441012',
        'name' => 'Editor Admin 2',
        'email' => 'editor-admin2@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/settings/email-templates/nonexistent_type/en')
        ->assertNotFound();
});

// ─── UpdateEmailTemplateController ─────────────────────────────────────────

it('updates an email template and redirects with success flash', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441020',
        'name' => 'Update Admin',
        'email' => 'update-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->put('/settings/email-templates/user_invite/en', [
            'subject_template' => 'Updated Subject {{userName}}',
            'body_template' => '<p>Updated body for {{userName}}</p>',
        ])
        ->assertRedirect('/settings/email-templates')
        ->assertSessionHas('success');
});

it('returns 403 on update without permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441021',
        'name' => 'No Update Perm',
        'email' => 'no-update-perm@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->put('/settings/email-templates/user_invite/en', [
            'subject_template' => 'Subject',
            'body_template' => 'Body',
        ])
        ->assertForbidden();
});

it('rejects update with missing subject template', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441022',
        'name' => 'Validator Admin',
        'email' => 'validator-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->from('/settings/email-templates/user_invite/en')
        ->put('/settings/email-templates/user_invite/en', [
            'body_template' => 'Body only',
        ])
        ->assertRedirect('/settings/email-templates/user_invite/en')
        ->assertSessionHasErrors('subject_template');
});

it('persists updated template content to the database', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441023',
        'name' => 'Persist Admin',
        'email' => 'persist-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->put('/settings/email-templates/user_invite/en', [
            'subject_template' => 'New subject',
            'body_template' => '<p>New body</p>',
        ]);

    $this->assertDatabaseHas('email_templates', [
        'type' => 'user_invite',
        'locale' => 'en',
        'subject_template' => 'New subject',
        'body_template' => '<p>New body</p>',
    ]);
});

// ─── ResetEmailTemplateController ──────────────────────────────────────────

it('resets email template to default and redirects with success flash', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441030',
        'name' => 'Reset Admin',
        'email' => 'reset-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    // First update the template to something custom
    EmailTemplateModel::where('type', 'user_invite')->where('locale', 'en')
        ->update(['subject_template' => 'Custom subject', 'body_template' => '<p>Custom</p>']);

    $this->actingAs($user)
        ->post('/settings/email-templates/user_invite/en/reset')
        ->assertRedirect('/settings/email-templates')
        ->assertSessionHas('success');
});

it('returns 403 on reset without permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441031',
        'name' => 'No Reset Perm',
        'email' => 'no-reset-perm@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->post('/settings/email-templates/user_invite/en/reset')
        ->assertForbidden();
});

it('redirects unauthenticated user to login from reset', function (): void {
    $this->post('/settings/email-templates/user_invite/en/reset')
        ->assertRedirect('/login');
});

// ─── PreviewEmailTemplateController ────────────────────────────────────────

it('returns rendered preview json for valid template', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441040',
        'name' => 'Preview Admin',
        'email' => 'preview-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->postJson('/settings/email-templates/preview', [
            'templateType' => 'user_invite',
            'locale' => 'en',
            'subjectTemplate' => 'Hello {{userName}}',
            'bodyTemplate' => '<p>Welcome {{userName}}</p>',
        ])
        ->assertOk()
        ->assertJsonStructure(['subject', 'html']);
});

it('returns 403 on preview without permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441041',
        'name' => 'No Preview Perm',
        'email' => 'no-preview-perm@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->postJson('/settings/email-templates/preview', [
            'templateType' => 'user_invite',
            'locale' => 'en',
            'subjectTemplate' => 'Hello',
            'bodyTemplate' => '<p>Body</p>',
        ])
        ->assertForbidden();
});

it('rejects preview request missing templateType', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441042',
        'name' => 'Preview Validator',
        'email' => 'preview-validator@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->postJson('/settings/email-templates/preview', [
            'locale' => 'en',
            'subjectTemplate' => 'Hello',
            'bodyTemplate' => '<p>Body</p>',
        ])
        ->assertUnprocessable();
});

// ─── ListEmailLogsController ────────────────────────────────────────────────

it('shows email logs index for user with permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441050',
        'name' => 'Logs Admin',
        'email' => 'logs-admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/settings/email-logs')
        ->assertOk()
        ->assertViewIs('settings.email-logs.index');
});

it('returns 403 on email logs index without permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441051',
        'name' => 'No Logs Perm',
        'email' => 'no-logs-perm@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/settings/email-logs')
        ->assertForbidden();
});

it('redirects unauthenticated user to login from email logs index', function (): void {
    $this->get('/settings/email-logs')
        ->assertRedirect('/login?'.http_build_query(['redirect' => '/settings/email-logs']));
});

it('displays email log entries on the index page', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441052',
        'name' => 'Log Viewer',
        'email' => 'log-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    EmailLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441099',
        'template_type' => 'user_invite',
        'locale' => 'en',
        'recipient_id' => $user->id,
        'recipient_email' => 'log-viewer@example.com',
        'rendered_subject' => 'Invite for Log Viewer',
        'rendered_body_masked' => '<p>Hello Log Viewer</p>',
        'variable_keys' => ['userName', 'link', 'tenantName'],
        'trace_id' => 'trace-log-test-001',
        'sent_at' => '2026-01-15 10:00:00',
    ]);

    $this->actingAs($user)
        ->get('/settings/email-logs')
        ->assertOk()
        ->assertSee('user_invite');
});

it('passes recipient_id query param and returns 200', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441053',
        'name' => 'Filter Viewer',
        'email' => 'filter-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/settings/email-logs?recipient_id='.$user->id)
        ->assertOk()
        ->assertViewIs('settings.email-logs.index');
});
