<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdatePasswordRequest;
use App\Mail\User\UpdatePassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UpdatePasswordAction
{
    /**
     * @param  UpdatePasswordRequest  $request
     * @return void
     */
    public function __invoke(UpdatePasswordRequest $request): void
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Mail::to($request->user())->send(new UpdatePassword($user));
    }
}
