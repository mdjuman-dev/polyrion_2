<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use App\Models\GlobalSetting;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appName = GlobalSetting::getValue('app_name') ?? config('app.name', 'Polyrion');
        $appUrl = GlobalSetting::getValue('app_url') ?? config('app.url', url('/'));
        
        // Get logo from settings
        $logo = GlobalSetting::getValue('logo');
        $logoUrl = null;
        if ($logo) {
            $logoUrl = str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo);
        }
        
        // Use Fortify's password reset route
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset()
        ]);

        // Get custom email template settings from database
        $subject = GlobalSetting::getValue('reset_password_subject') ?? __('Reset Password Notification');
        $greeting = GlobalSetting::getValue('reset_password_greeting') ?? __('Hello :name!');
        $line1 = GlobalSetting::getValue('reset_password_line1') ?? __('You are receiving this email because we received a password reset request for your account.');
        $actionText = GlobalSetting::getValue('reset_password_action_text') ?? __('Reset Password');
        $line2 = GlobalSetting::getValue('reset_password_line2') ?? __('This password reset link will expire in :count minutes.');
        $line3 = GlobalSetting::getValue('reset_password_line3') ?? __('If you did not request a password reset, no further action is required.');
        $salutation = GlobalSetting::getValue('reset_password_salutation') ?? __('Regards,');

        // Replace placeholders
        $expireMinutes = config('auth.passwords.users.expire', 60);
        $greeting = str_replace(':name', $notifiable->name, $greeting);
        $line2 = str_replace(':count', $expireMinutes, $line2);
        $salutation = $salutation . "\n" . $appName;

        // If logo is available, use custom view
        if ($logoUrl) {
            return (new MailMessage)
                ->subject($subject)
                ->view('emails.reset-password', [
                    'logoUrl' => $logoUrl,
                    'appName' => $appName,
                    'appUrl' => $appUrl,
                    'subject' => $subject,
                    'greeting' => $greeting,
                    'line1' => $line1,
                    'actionText' => $actionText,
                    'actionUrl' => $url,
                    'line2' => $line2,
                    'line3' => $line3,
                    'salutation' => $salutation,
                ]);
        }

        // Default MailMessage without logo
        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line1)
            ->action($actionText, $url)
            ->line($line2)
            ->line($line3)
            ->salutation($salutation);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
