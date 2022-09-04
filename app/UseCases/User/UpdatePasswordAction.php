<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordAction
{
    /**
     * @param UpdatePasswordRequest $request
     * @return void
     */
    public function __invoke(UpdatePasswordRequest $request): void
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password)
        ]);
    }
}
