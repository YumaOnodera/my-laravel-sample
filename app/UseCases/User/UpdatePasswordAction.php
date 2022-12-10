<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdatePasswordRequest;
use App\Mail\User\UpdatePassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UpdatePasswordAction
{
    /**
     * @param  UpdatePasswordRequest  $request
     * @param  int  $id
     * @return void
     */
    public function __invoke(UpdatePasswordRequest $request, int $id): void
    {
        $user = User::where('id', $id);

        $user->update([
            'password' => Hash::make($request->password),
        ]);
        $user->searchable();

        Mail::to($request->user())->send(new UpdatePassword($user->first()));
    }
}
