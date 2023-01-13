<?php

namespace App\UseCases\User;

use App\Http\Requests\User\RestoreRequest;
use App\Mail\Auth\Restore;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class RestoreAction
{
    /**
     * @param  RestoreRequest  $request
     * @param  int  $id
     * @return void
     */
    public function __invoke(RestoreRequest $request, int $id): void
    {
        $user = User::withTrashed()->findOrFail($id);

        $user->restore();
        $user->searchable();

        Mail::to($user)->send(new Restore($user));
    }
}
