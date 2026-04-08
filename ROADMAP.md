# Roadmap

## Tenant configuration
### Email templating 
- Including login, password reset, etc... extendable and reusable from any Domain, template per translation.
### Enforce password rotation 
- Including validation, that it does not match last password, including notifications when time approaches.. notification should appear on every login in last 10% of the rotation period.
### 2FA 
- (Email OTP or 2FA app, user can choose) + password enforcement in the settings per tenant.
### SSO
- Search some Laravel implementation package, if we can reuse this to offer some of the generic SSO options, tenant would. SSO bypasses 2FA optionally.
### Enable overriding default email provider
- Offer Mailjet, Mailgun and MailPit for now. Should even send invite and password recovery emails through tenant defined email provider, using their from address.

## Tenant broadcast
- Sending notification to whole tenant, explicit users, whole teams or teams including subteams. (Text-length limited, simple text)
- Sending email to whole tenant, explicit users, whole teams or teams including subteams. (Large text limit, rich text, including attachments)

## Audit log
- Log every Command to database for audit logging. Add this as listener to domain events, group together by trace-id. There should be history, viewable per entity or per user. Viewing history should be permission based. History cannot be edited nor deleted.

## Webhook system
- Users can configure outbound webhooks for any domain event. In configuration, he should see example request and even can trigger test webhook.

## API Key Management
- Per user API key management, respecting users permissions.
- Per tenant API key management, selecting exact permissions for the key. Can include root team in case of selecting team scoped permissions.
- No-one can create API key with wider permissions than he has.

## Billing
- Add billing platform, with optional payment gateway adapter and configuration. Start with GoPay and Stripe adapter options.
- By file configurable price tiers, free test periods, user limits and features (reusing feature flags).
