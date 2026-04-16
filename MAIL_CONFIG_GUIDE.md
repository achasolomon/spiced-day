# Mail Configuration Guide

## Quick Fix for Development

If you're having timeout issues with email, use the `log` driver for development:

```env
MAIL_MAILER=log
```

This will write emails to `storage/logs/laravel.log` instead of trying to send them, preventing timeouts.

## Production SMTP Configuration

For production, configure your SMTP settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SPICE'd Dayhome"
```

## Common SMTP Providers

### Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

**Note:** Gmail requires an "App Password" - not your regular password.

### Outlook/Office 365
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Custom SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

## Testing Your Configuration

After updating your `.env` file, test the configuration:

```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email', function ($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

## Important Notes

1. **Timeout Protection**: The system now has a 5-second timeout on SMTP connections to prevent long waits
2. **Queue Support**: Emails are queued to prevent blocking, even if mail fails
3. **Fallback**: If email fails, verification codes are shown directly to users
4. **Admin Users**: Users created by admins are automatically verified and don't need email verification

## Troubleshooting

### Timeout Errors
- Check your SMTP server is accessible
- Verify port is correct (587 for TLS, 465 for SSL)
- Check firewall settings
- Use `log` driver for development

### Connection Refused
- Verify SMTP host and port
- Check if your hosting provider blocks SMTP ports
- Try using a mail service like Mailgun or SendGrid

### Authentication Failed
- Double-check username and password
- For Gmail, use App Password, not regular password
- Ensure account is not locked or restricted


