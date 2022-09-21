<?php

namespace App\UseCases\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ShowAction
{
    /**
     * @param int $id
     * @return Model
     */
    public function __invoke(int $id): Model
    {
        return User::withTrashed()->findOrFail($id);
    }
}
