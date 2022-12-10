<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UpdateAction
{
    /**
     * @param  UpdateRequest  $request
     * @param  int  $id
     * @return Model
     */
    public function __invoke(UpdateRequest $request, int $id): Model
    {
        $user = User::where('id', $id);

        $user->update([
            'name' => $request->name,
        ]);
        $user->searchable();

        return $user->first();
    }
}
