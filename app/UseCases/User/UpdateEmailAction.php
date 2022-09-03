<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdateEmailRequest;
use App\Models\User;

class UpdateEmailAction
{
    /**
     * @param UpdateEmailRequest $request
     * @return void
     */
    public function __invoke(UpdateEmailRequest $request): void
    {
        $user = User::where('id', auth()->id());

        $user->update([
            'email' => $request->email,
            'email_verified_at' => null
        ]);
    }
}
