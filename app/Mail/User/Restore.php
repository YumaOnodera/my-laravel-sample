<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Restore extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Model
     */
    protected Model $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Model $user)
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
        return $this->markdown('emails.users.restore', [
            'user' => $this->user->name,
        ])
            ->subject(__('mail.restore.subject', ['appName' => config('app.name')]));
    }
}
