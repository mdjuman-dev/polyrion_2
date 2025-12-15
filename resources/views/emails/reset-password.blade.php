<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Reset Password' }}</title>
</head>

<body
    style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Logo Header -->
                    @if (isset($logoUrl))
                        <tr>
                            <td style="padding: 30px 20px 20px; text-align: center; background-color: #ffffff;">
                                <img src="{{ $logoUrl }}" alt="{{ $appName ?? 'Logo' }}"
                                    style="max-height: 60px; max-width: 200px; height: auto;">
                            </td>
                        </tr>
                    @endif

                    <!-- Content -->
                    <tr>
                        <td style="padding: 20px 40px;">
                            <h1 style="color: #333333; font-size: 24px; margin: 0 0 20px;">{{ $greeting ?? 'Hello!' }}
                            </h1>

                            <p style="color: #666666; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                {{ $line1 ?? '' }}
                            </p>

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $actionUrl ?? '#' }}"
                                    style="display: inline-block; padding: 12px 30px; background-color: #667eea; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                    {{ $actionText ?? 'Reset Password' }}
                                </a>
                            </div>

                            <p style="color: #666666; font-size: 16px; line-height: 1.6; margin: 20px 0;">
                                {{ $line2 ?? '' }}
                            </p>

                            <p style="color: #666666; font-size: 16px; line-height: 1.6; margin: 20px 0;">
                                {{ $line3 ?? '' }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="padding: 20px 40px; background-color: #f8f9fa; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="color: #999999; font-size: 14px; margin: 0;">
                                {{ $salutation ?? 'Regards' }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
