<?php

namespace App\UseCases\User;

use App\Http\Requests\User\DestroyRequest;
use App\Mail\User\Destroy;
use App\Models\User;
use App\Models\UserRestore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DestroyAction
{
    /**
     * @param  DestroyRequest  $request
     * @param  int  $id
     * @return void
     */
    public function __invoke(DestroyRequest $request, int $id): void
    {
        $user = User::findOrFail($id);
        $requestUser = $request->user();

        DB::transaction(static function () use ($user, $requestUser, $id) {
            $user->delete();
            $user->searchable();

            if (
                ($requestUser->is_admin && $requestUser->id === $id)
                || ! $requestUser->is_admin
            ) {
                UserRestore::create([
                    'user_id' => $id,
                    'token' => Str::random(60),
                ]);
            }
        });

        Mail::to($user)->send(new Destroy($user));
    }
}
