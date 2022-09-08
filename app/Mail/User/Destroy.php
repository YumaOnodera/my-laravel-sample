<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Destroy extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected User $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.users.destroy', [
            'user' => $this->user->name,
        ])
            ->subject(__('mail.destroy.subject', ['appName' => config('app.name')]));
    }
}
