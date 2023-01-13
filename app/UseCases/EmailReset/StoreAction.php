<?php

namespace App\UseCases\EmailReset;

use App\Http\Requests\EmailReset\StoreRequest;
use App\Mail\EmailReset\Store;
use App\Models\EmailReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StoreAction
{
    /**
     * @param  StoreRequest  $request
     * @return void
     */
    public function __invoke(StoreRequest $request): void
    {
        $requestUser = $request->user();

        $emailReset = DB::transaction(static function () use ($requestUser, $request) {
            EmailReset::where('user_id', $requestUser->id)->delete();

            return EmailReset::create([
                'user_id' => $requestUser->id,
                'new_email' => $request->new_email,
                'token' => Str::random(60),
            ]);
        });

        Mail::to($emailReset->new_email)->send(new Store($emailReset->token));
    }
}
