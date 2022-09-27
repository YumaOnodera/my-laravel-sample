<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $resetPasswordUrl = $this->resetUrl($notifiable);
        $expiration = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject(__('mail.reset_password.subject'))
            ->line(__('mail.reset_password.line_01'))
            ->action(__('mail.reset_password.action'), $resetPasswordUrl)
            ->lines([
                __('mail.reset_password.line_02', ['count' => $expiration]),
                __('mail.reset_password.line_03'),
            ]);
    }
}
