<?php

namespace App\UseCases\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ShowAction
{
    /**
     * @param  Request  $request
     * @param  int  $id
     * @return Model
     */
    public function __invoke(Request $request, int $id): Model
    {
        $users = User::query();

        if ($request->user()?->is_admin) {
            $users = $users->withTrashed();
        }

        return $users->findOrFail($id);
    }
}
