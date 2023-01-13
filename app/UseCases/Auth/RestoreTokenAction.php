<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\UserRestore;
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
            return UserRestore::where('user_id', $user->id)->first()?->token;
        }

        return null;
    }
}
