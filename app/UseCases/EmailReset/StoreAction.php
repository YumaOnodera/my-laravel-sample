<?php

namespace App\UseCases\EmailReset;

use App\Http\Requests\EmailReset\StoreRequest;
use App\Mail\EmailReset\Store;
use App\Models\EmailReset;
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
        $emailReset = EmailReset::create([
            'user_id' => $request->user()->id,
            'new_email' => $request->new_email,
            'token' => Str::random(60),
        ]);

        Mail::to($emailReset->new_email)->send(new Store($emailReset->token));
    }
}
