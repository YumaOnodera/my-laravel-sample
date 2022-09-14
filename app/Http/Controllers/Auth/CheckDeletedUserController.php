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
        return response()->json(['is_deleted' => $action($request)]);
    }
}
