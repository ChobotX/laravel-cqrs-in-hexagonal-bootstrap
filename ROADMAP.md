# Roadmap

Planned work that is **not** yet in the codebase (implemented areas such as email templating and audit logging live in the app and layer READMEs).

## Tenant configuration

### Enable overriding default email provider

- Offer Mailjet, Mailgun, and Mailpit (or equivalent) as tenant-configurable transports. Invite and password recovery should respect the tenant’s provider and from address.

## Tenant broadcast

- Notifications to the whole tenant, explicit users, whole teams, or teams including subteams (text-length limited, plain text).
- Email to the same audiences (higher length limit, rich text, attachments).

## Webhook system

- Users configure outbound webhooks for domain events, see an example request payload, and can fire a test delivery.

## API key management

- Per-user API keys that respect that user’s permissions.
- Per-tenant API keys with explicit permission grants (including root team when using team-scoped permissions).
- No key may be created with broader permissions than the creator has.
- Maximum API key lifetime should be configurable at tenant configuration, with default to be 1 year. Can be set to unlimited.

## Billing

- Billing platform with an optional payment gateway adapter. Initial adapters: GoPay and Stripe.
- File-driven price tiers, trial periods, user limits, and feature gating (reusing feature flags).

