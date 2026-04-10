<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="IE=edge">
    <title>{{ $tenantName }}</title>
</head>

<body
      style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table role="presentation"
           style="background-color: #f3f4f6;"
           width="100%"
           cellpadding="0"
           cellspacing="0">
        <tr>
            <td style="padding: 40px 16px;"
                align="center">
                <table role="presentation"
                       style="max-width: 600px; width: 100%;"
                       width="600"
                       cellpadding="0"
                       cellspacing="0">
                    {{-- Header --}}
                    <tr>
                        <td style="padding-bottom: 24px;"
                            align="center">
                            @if ($tenantLogoUrl)
                                <img src="{{ $tenantLogoUrl }}"
                                     alt="{{ $tenantName }}"
                                     style="display: block; border-radius: 8px; margin-bottom: 8px;"
                                     width="48"
                                     height="48">
                            @endif
                            <span style="font-size: 18px; font-weight: 600; color: #111827;">{{ $tenantName }}</span>
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td
                            style="background-color: #ffffff; border-radius: 12px; padding: 40px 32px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                            <div style="color: #374151; font-size: 16px; line-height: 1.6;">
                                {!! $body !!}
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding-top: 24px;"
                            align="center">
                            <p style="margin: 0; font-size: 13px; color: #9ca3af; line-height: 1.5;">
                                {{ __('messages.email_templates.email_footer', ['tenant' => $tenantName]) }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
