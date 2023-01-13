<?php

namespace App\UseCases\PasswordReset;

use App\Http\Requests\PasswordReset\UpdateRequest;
use App\Mail\UpdatePassword\Update;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UpdateAction
{
    /**
     * @param  UpdateRequest  $request
     * @return void
     */
    public function __invoke(UpdateRequest $request): void
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Mail::to($user)->send(new Update($user));
    }
}
