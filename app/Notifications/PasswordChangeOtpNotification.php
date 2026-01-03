<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\GlobalSetting;

class PasswordChangeOtpNotification extends Notification
{
    use Queueable;

    /**
     * The OTP code.
     *
     * @var string
     */
    public $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otp)
    {
        $this->otp = $otp;
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

        $subject = 'Password Change OTP - ' . $appName;
        $greeting = 'Hello ' . $notifiable->name . '!';
        $line1 = 'You requested to change your password. Please use the following OTP code to verify your identity:';
        $line2 = 'This OTP will expire in 10 minutes.';
        $line3 = 'If you did not request this password change, please ignore this email.';
        $salutation = 'Regards,' . "\n" . $appName;

        // If logo is available, use custom view
        if ($logoUrl) {
            return (new MailMessage)
                ->subject($subject)
                ->view('emails.password-change-otp', [
                    'logoUrl' => $logoUrl,
                    'appName' => $appName,
                    'appUrl' => $appUrl,
                    'subject' => $subject,
                    'greeting' => $greeting,
                    'line1' => $line1,
                    'otp' => $this->otp,
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
            ->line('**Your OTP Code: ' . $this->otp . '**')
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

