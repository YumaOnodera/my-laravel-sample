<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Mail\Auth\Restore;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class RestoreAction
{
    /**
     * @param  LoginRequest  $request
     * @return void
     */
    public function __invoke(LoginRequest $request): void
    {
        $user = User::withTrashed()
            ->where('email', $request->email)
            ->whereNotNull('deleted_at')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw new RuntimeException(__('exception.auth.restore'));
        }

        $user->restore();
        $user->searchable();

        Mail::to($user)->send(new Restore($user));
    }
}
