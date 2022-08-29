<?php

namespace App\UseCases\User;

use App\Models\User;

class DestroyAction
{
    /**
     * @param int $id
     * @return bool
     */
    public function __invoke(int $id): bool
    {
        return User::findOrFail($id)->delete();
    }
}
