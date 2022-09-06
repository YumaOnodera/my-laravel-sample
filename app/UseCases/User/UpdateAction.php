<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdateRequest;
use Illuminate\Database\Eloquent\Model;

class UpdateAction
{
    /**
     * @param UpdateRequest $request
     * @return Model
     */
    public function __invoke(UpdateRequest $request): Model
    {
        $user = $request->user();

        $user->update([
            'name' => $request->name,
        ]);

        return $user->first();
    }
}
