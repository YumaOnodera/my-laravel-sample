<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerification extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return (new MailMessage)
            ->subject(__('mail.email_verification.subject'))
            ->lines([
                __('mail.email_verification.line_01', [
                    'appName' => config('app.name'),
                    'user' => $notifiable->name,
                ]),
                __('mail.email_verification.line_02'),
            ])
            ->action(__('mail.email_verification.action'), $verificationUrl)
            ->line(__('mail.email_verification.line_03'))
            ->salutation(__('mail.common.salutation', ['appName' => config('app.name')]));
    }
}
