<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CheckDeletedUserAction
{
    /**
     * @param LoginRequest $request
     * @return bool
     */
    public function __invoke(LoginRequest $request): bool
    {
        $user = User::withTrashed()
            ->where('email', $request->email)
            ->whereNotNull('deleted_at')
            ->first();

        return $user && Hash::check($request->password, $user->password);
    }
}
