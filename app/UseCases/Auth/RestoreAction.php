<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\RestoreRequest;
use App\Mail\Auth\Restore;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class RestoreAction
{
    /**
     * @param  RestoreRequest  $request
     * @return void
     */
    public function __invoke(RestoreRequest $request): void
    {
        $user = User::withTrashed()
            ->where('restore_token', $request->restore_token)
            ->whereNotNull('deleted_at')
            ->first();

        if (! $user) {
            throw new RuntimeException(__('exception.auth.restore', ['admin' => config('app.name')]));
        }

        $user->restore();
        $user->forceFill([
            'restore_token' => null,
        ])->save();
        $user->searchable();

        Mail::to($user)->send(new Restore($user));
    }
}
