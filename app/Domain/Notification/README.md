# Notification Module

Enterprise notification system with multi-channel delivery, user preferences, and real-time in-app updates.

## Domain Model

- **Notification** — immutable entity: id, recipientId, type, title, body, level, link, channel, isRead, timestamps
- **NotificationPreferences** — per-user channel preferences per notification level
- **NotificationLevel** — enum: Info, Success, Warning, Error
- **NotificationChannel** — enum: InApp, Email (extensible for Push)
- **NotificationType** — slug-validated type identifier (e.g. `user.welcome`)
- **NotificationLink** — relative URL value object (must start with `/`)

## Commands

| Command | Handler | Permission | Description |
|---------|---------|------------|-------------|
| `SendNotificationCommand` | `SendNotificationHandler` | SkipPermissionCheck (internal) | Send notification to list of recipient IDs |
| `MarkNotificationAsReadCommand` | `MarkNotificationAsReadHandler` | SkipPermissionCheck (ownership in handler) | Mark single notification as read |
| `MarkAllNotificationsAsReadCommand` | `MarkAllNotificationsAsReadHandler` | SkipPermissionCheck | Mark all user's notifications as read |
| `DeleteNotificationCommand` | `DeleteNotificationHandler` | SkipPermissionCheck (ownership in handler) | Delete single notification |
| `UpdateNotificationPreferencesCommand` | `UpdateNotificationPreferencesHandler` | SkipPermissionCheck | Update user's channel preferences |

## Queries

| Query | Handler | Returns |
|-------|---------|---------|
| `ListOwnNotificationsQuery` | `ListOwnNotificationsHandler` | `PaginatedResult<Notification>` |
| `CountUnreadNotificationsQuery` | `CountUnreadNotificationsHandler` | `int` |
| `GetNotificationPreferencesQuery` | `GetNotificationPreferencesHandler` | `NotificationPreferences` |

## Events

- `NotificationCreated` — emitted per in-app notification created
- `NotificationRead` — emitted when a single notification is marked as read
- `AllNotificationsRead` — emitted when all notifications are marked as read
- `NotificationDeleted` — emitted when a notification is deleted

## Event Handlers

**Domain** (`App\Domain\Notification\EventHandler\`):
- `CleanupNotificationsOnUserDeleted` — listens to `UserDeleted`, removes all notifications

**Infrastructure** (`App\Infrastructure\Notification\EventHandler\`):
- `SendWelcomeNotificationOnUserCreated` — listens to `UserCreated`, dispatches `SendNotificationCommand` (in Infrastructure because it uses `CommandBus`)
- `BroadcastNotificationCreated` — broadcasts new in-app notifications via WebSocket
- `BroadcastUnreadCountUpdated` — broadcasts updated unread count after read/delete events

## Channel System

The `SendNotificationHandler` resolves channels per recipient based on their preferences (defaults: info/success→in_app only, warning/error→in_app+email). In-app notifications are persisted directly by the handler. External channels (email, future push) use the `NotificationChannelSender` contract, resolved via `NotificationChannelSenderRegistry`.

## Recipient Resolution

For team/subteam targeting, the `RecipientResolver` contract resolves team hierarchies to user ID lists. The `EloquentRecipientResolver` uses a recursive CTE query. The domain handler receives pre-resolved `list<string>` of recipient IDs — no cross-domain coupling.
