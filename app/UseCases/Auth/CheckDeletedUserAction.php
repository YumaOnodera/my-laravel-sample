<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CheckDeletedUserAction
{
    /**
     * @param LoginRequest $request
     * @return array
     */
    public function __invoke(LoginRequest $request): array
    {
        $result = [
            'id' => null,
            'is_deleted' => false
        ];

        $user = User::withTrashed()
            ->where('email', $request->email)
            ->whereNotNull('deleted_at')
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $result['id'] = $user->id;
            $result['is_deleted'] = true;
        }

        return $result;
    }
}
