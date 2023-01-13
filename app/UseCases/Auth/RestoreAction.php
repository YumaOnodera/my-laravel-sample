<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\RestoreRequest;
use App\Mail\Auth\Restore;
use App\Models\User;
use App\Models\UserRestore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RestoreAction
{
    /**
     * @param  RestoreRequest  $request
     * @param  string  $token
     * @return void
     */
    public function __invoke(RestoreRequest $request, string $token): void
    {
        $userRestore = UserRestore::where('token', $token)->first();
        $user = User::withTrashed()->findOrFail($userRestore->user_id);

        DB::transaction(static function () use ($user, $userRestore) {
            $user->restore();
            $user->searchable();

            $userRestore->delete();
        });

        Mail::to($user)->send(new Restore($user));
    }
}
