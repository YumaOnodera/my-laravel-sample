<?php

namespace App\UseCases\User;

use App\Http\Requests\User\DestroyRequest;
use App\Mail\User\Destroy;
use App\Models\User;
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

        $user->delete();
        $user->forceFill([
            'restore_token' => Str::random(60),
        ])->save();
        $user->searchable();

        Mail::to($request->user())
            ->cc($user)
            ->send(new Destroy($user));
    }
}
