<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\UseCases\Auth\CheckDeletedUserAction;
use Illuminate\Http\JsonResponse;

class CheckDeletedUserController extends Controller
{
    /**
     * メールアドレスとパスワードをもとにユーザーを取得し、対象ユーザーが削除済みかどうかを判定する。
     *
     * @param LoginRequest $request
     * @param CheckDeletedUserAction $action
     * @return JsonResponse
     */
    public function isDeleted(LoginRequest $request, CheckDeletedUserAction $action): JsonResponse
    {
        $result = $action($request);

        return response()->json([
            'id' => $result['id'],
            'is_deleted' => $result['is_deleted']
        ]);
    }
}
