<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Notification\NotificationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function createNotificationUser(): UserModel
{
    return UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544e000',
        'name' => 'Notification User',
        'email' => 'notification-user@test.com',
        'password' => Hash::make('password'),
    ]);
}

function createNotificationModel(string $id, string $recipientId, bool $isRead = false): NotificationModel
{
    return NotificationModel::create([
        'id' => $id,
        'recipient_id' => $recipientId,
        'type' => 'user.welcome',
        'title' => 'Welcome!',
        'body' => 'Welcome to the platform.',
        'level' => 'info',
        'link' => '/profile',
        'channel' => 'in_app',
        'is_read' => $isRead,
        'read_at' => $isRead ? '2026-01-15 11:00:00' : null,
    ]);
}

it('requires authentication for listing notifications', function (): void {
    $this->getJson('/internal-api/notifications')
        ->assertUnauthorized();
});

it('lists notifications for authenticated user', function (): void {
    $userModel = createNotificationUser();
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $userModel->id);
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e002', $userModel->id, true);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/notifications');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'level', 'title', 'body', 'link_url', 'read_at', 'created_at']],
            'meta' => ['current_page', 'per_page', 'total', 'total_pages'],
        ]);
});

it('filters notifications by unread', function (): void {
    $userModel = createNotificationUser();
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $userModel->id);
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e002', $userModel->id, true);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/notifications?filter=unread');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.read_at', null);
});

it('filters notifications by read', function (): void {
    $userModel = createNotificationUser();
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $userModel->id);
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e002', $userModel->id, true);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/notifications?filter=read');

    $response->assertOk()
        ->assertJsonCount(1, 'data');
});

it('validates filter parameter', function (): void {
    $userModel = createNotificationUser();

    $this->actingAs($userModel)
        ->getJson('/internal-api/notifications?filter=invalid')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['filter']);
});

it('paginates notifications', function (): void {
    $userModel = createNotificationUser();
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $userModel->id);
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e002', $userModel->id);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/notifications?page=1&per_page=1');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('meta.total', 2)
        ->assertJsonPath('meta.per_page', 1);
});

it('requires authentication for unread count', function (): void {
    $this->getJson('/internal-api/notifications/unread-count')
        ->assertUnauthorized();
});

it('returns unread count', function (): void {
    $userModel = createNotificationUser();
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $userModel->id);
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e002', $userModel->id, true);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/notifications/unread-count');

    $response->assertOk()
        ->assertJsonPath('count', 1);
});

it('requires authentication for mark read', function (): void {
    $this->postJson('/internal-api/notifications/550e8400-e29b-41d4-a716-44665544e001/mark-read')
        ->assertUnauthorized();
});

it('marks notification as read', function (): void {
    $userModel = createNotificationUser();
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $userModel->id);

    $this->actingAs($userModel)
        ->postJson('/internal-api/notifications/550e8400-e29b-41d4-a716-44665544e001/mark-read')
        ->assertNoContent();

    $this->assertDatabaseHas('notifications', [
        'id' => '550e8400-e29b-41d4-a716-44665544e001',
        'is_read' => true,
    ]);
});

it('requires authentication for mark all read', function (): void {
    $this->postJson('/internal-api/notifications/mark-all-read')
        ->assertUnauthorized();
});

it('marks all notifications as read', function (): void {
    $userModel = createNotificationUser();
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $userModel->id);
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e002', $userModel->id);

    $this->actingAs($userModel)
        ->postJson('/internal-api/notifications/mark-all-read')
        ->assertNoContent();

    expect(NotificationModel::where('recipient_id', $userModel->id)->where('is_read', false)->count())->toBe(0);
});

it('requires authentication for delete', function (): void {
    $this->deleteJson('/internal-api/notifications/550e8400-e29b-41d4-a716-44665544e001')
        ->assertUnauthorized();
});

it('deletes notification', function (): void {
    $userModel = createNotificationUser();
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $userModel->id);

    $this->actingAs($userModel)
        ->deleteJson('/internal-api/notifications/550e8400-e29b-41d4-a716-44665544e001')
        ->assertNoContent();

    $this->assertDatabaseMissing('notifications', [
        'id' => '550e8400-e29b-41d4-a716-44665544e001',
    ]);
});

it('prevents deleting another users notification', function (): void {
    $userModel = createNotificationUser();
    $otherUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544e099',
        'name' => 'Other User',
        'email' => 'other-notif-user@test.com',
        'password' => Hash::make('password'),
    ]);
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $otherUser->id);

    $this->actingAs($userModel)
        ->deleteJson('/internal-api/notifications/550e8400-e29b-41d4-a716-44665544e001')
        ->assertForbidden();
});

it('prevents marking another users notification as read', function (): void {
    $userModel = createNotificationUser();
    $otherUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544e098',
        'name' => 'Other User',
        'email' => 'other-notif-mark@test.com',
        'password' => Hash::make('password'),
    ]);
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $otherUser->id);

    $this->actingAs($userModel)
        ->postJson('/internal-api/notifications/550e8400-e29b-41d4-a716-44665544e001/mark-read')
        ->assertForbidden();
});

it('does not show other users notifications in list', function (): void {
    $userModel = createNotificationUser();
    $otherUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544e097',
        'name' => 'Other User',
        'email' => 'other-notif-list@test.com',
        'password' => Hash::make('password'),
    ]);
    createNotificationModel('550e8400-e29b-41d4-a716-44665544e001', $otherUser->id);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/notifications');

    $response->assertOk()
        ->assertJsonCount(0, 'data');
});
