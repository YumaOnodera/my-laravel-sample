<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdateEmailRequest;

class UpdateEmailAction
{
    /**
     * @param  UpdateEmailRequest  $request
     * @return void
     */
    public function __invoke(UpdateEmailRequest $request): void
    {
        $user = $request->user();

        $user->update([
            'email' => $request->email,
            'email_verified_at' => null,
        ]);

        $user->sendEmailVerificationNotification();
    }
}
