<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Eloquent\Notification\NotificationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        /** @var list<string> $userIds */
        $userIds = UserModel::pluck('id')->all();

        $this->seedWelcomeNotifications($userIds);
        $this->seedAdminExampleNotifications();
    }

    /** @param list<string> $userIds */
    private function seedWelcomeNotifications(array $userIds): void
    {
        foreach ($userIds as $userId) {
            NotificationModel::create([
                'id' => Str::uuid()->toString(),
                'recipient_id' => $userId,
                'type' => 'user.welcome',
                'title' => 'Welcome!',
                'body' => 'Welcome to the platform. You can manage your notification preferences in your profile settings.',
                'level' => 'info',
                'link' => '/profile',
                'channel' => 'in_app',
                'is_read' => false,
            ]);
        }
    }

    private function seedAdminExampleNotifications(): void
    {
        $admin = UserModel::where('email', 'admin@test.com')->first();

        if (! $admin instanceof UserModel) {
            return;
        }

        $now = now();

        $notifications = [
            ['type' => 'team.member_joined', 'title' => 'New team member', 'body' => 'Henry Park joined the Engineering team.', 'level' => 'info', 'link' => '/teams', 'is_read' => true, 'read_at' => $now->copy()->subHours(2), 'created_at' => $now->copy()->subDays(3)],
            ['type' => 'system.update', 'title' => 'System update completed', 'body' => 'The scheduled maintenance has been completed successfully.', 'level' => 'success', 'link' => null, 'is_read' => true, 'read_at' => $now->copy()->subDay(), 'created_at' => $now->copy()->subDays(2)],
            ['type' => 'team.role_changed', 'title' => 'Role assignment changed', 'body' => 'Sarah Blake has been promoted to Team Leader in the Design team.', 'level' => 'info', 'link' => '/users', 'is_read' => false, 'read_at' => null, 'created_at' => $now->copy()->subDay()],
            ['type' => 'system.warning', 'title' => 'Storage quota warning', 'body' => 'Tenant storage usage has reached 85% of the allocated quota.', 'level' => 'warning', 'link' => null, 'is_read' => false, 'read_at' => null, 'created_at' => $now->copy()->subHours(18)],
            ['type' => 'security.login_anomaly', 'title' => 'Unusual login detected', 'body' => 'A login from an unrecognized device was detected for user raj.patel@test.com.', 'level' => 'warning', 'link' => '/users', 'is_read' => false, 'read_at' => null, 'created_at' => $now->copy()->subHours(6)],
            ['type' => 'system.error', 'title' => 'Background job failed', 'body' => 'The nightly report generation job failed after 3 retries.', 'level' => 'error', 'link' => null, 'is_read' => false, 'read_at' => null, 'created_at' => $now->copy()->subHours(3)],
            ['type' => 'team.invitation_accepted', 'title' => 'Invitation accepted', 'body' => 'Fiona Grant accepted the invitation to join the Brand & Identity team.', 'level' => 'success', 'link' => '/teams', 'is_read' => false, 'read_at' => null, 'created_at' => $now->copy()->subHour()],
            ['type' => 'security.permissions_changed', 'title' => 'Permissions updated', 'body' => 'Permission overrides were modified for user Wendy Cruz by Eva Collins.', 'level' => 'info', 'link' => '/users', 'is_read' => false, 'read_at' => null, 'created_at' => $now->copy()->subMinutes(30)],
        ];

        foreach ($notifications as $notification) {
            NotificationModel::create([
                'id' => Str::uuid()->toString(),
                'recipient_id' => $admin->id,
                'type' => $notification['type'],
                'title' => $notification['title'],
                'body' => $notification['body'],
                'level' => $notification['level'],
                'link' => $notification['link'],
                'channel' => 'in_app',
                'is_read' => $notification['is_read'],
                'read_at' => $notification['read_at'] ?? null,
                'created_at' => $notification['created_at'],
            ]);
        }
    }
}
