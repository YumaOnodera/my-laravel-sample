<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdateEmailRequest;
use App\Models\User;

class UpdateEmailAction
{
    /**
     * @param  UpdateEmailRequest  $request
     * @param  int  $id
     * @return void
     */
    public function __invoke(UpdateEmailRequest $request, int $id): void
    {
        $user = User::where('id', $id);

        $user->update([
            'email' => $request->email,
            'email_verified_at' => null,
        ]);
        $user->searchable();

        $user->first()->sendEmailVerificationNotification();
    }
}
