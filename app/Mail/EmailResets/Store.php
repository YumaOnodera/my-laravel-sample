<?php

namespace App\Mail\EmailResets;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Store extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    protected string $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.email-resets.store', [
            'actionUrl' => config('app.frontend_url').'/settings/accounts/email?token='.$this->token,
            'expiration' => config('const.email_resets.expire'),
        ])
            ->subject(__('mail.reset_email.subject'));
    }
}
