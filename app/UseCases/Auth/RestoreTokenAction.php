<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RestoreTokenAction
{
    /**
     * @param  LoginRequest  $request
     * @return string|null
     */
    public function __invoke(LoginRequest $request): string|null
    {
        $user = User::withTrashed()
            ->where('email', $request->email)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return $user->makeVisible(['restore_token'])->restore_token;
        }

        return null;
    }
}
