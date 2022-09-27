<?php

namespace App\UseCases\User;

use App\Http\Requests\User\DestroyRequest;
use App\Mail\User\Destroy;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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

        Mail::to($request->user())
            ->cc($user)
            ->send(new Destroy($user));
    }
}
