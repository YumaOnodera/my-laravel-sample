<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdatePermissionRequest;
use App\Models\User;

class UpdatePermissionAction
{
    /**
     * @param  UpdatePermissionRequest  $request
     * @param  int  $id
     * @return void
     */
    public function __invoke(UpdatePermissionRequest $request, int $id): void
    {
        $user = User::where('id', $id);

        $user->update([
            'is_admin' => $request->is_admin,
        ]);
        $user->searchable();
    }
}
