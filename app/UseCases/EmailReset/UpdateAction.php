<?php

namespace App\UseCases\EmailReset;

use App\Http\Requests\EmailReset\UpdateRequest;
use App\Models\EmailReset;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateAction
{
    /**
     * @param  UpdateRequest  $request
     * @param  string  $token
     * @return void
     */
    public function __invoke(UpdateRequest $request, string $token): void
    {
        $emailReset = EmailReset::where('token', $token)->first();
        $user = User::where('id', $emailReset->user_id);

        DB::transaction(static function () use ($emailReset, $user) {
            $user->update([
                'email' => $emailReset->new_email,
                'email_verified_at' => now(),
            ]);
            $user->searchable();

            $emailReset->delete();
        });
    }
}
