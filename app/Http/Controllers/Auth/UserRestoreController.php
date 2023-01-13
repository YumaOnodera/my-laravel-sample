<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RestoreRequest;
use App\UseCases\Auth\RestoreAction;
use App\UseCases\Auth\RestoreTokenAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserRestoreController extends Controller
{
    /**
     * メールアドレスとパスワードをもとにユーザーを検索し、対象ユーザーの復活用トークンを取得する。
     *
     * @param  LoginRequest  $request
     * @param  RestoreTokenAction  $action
     * @return JsonResponse
     */
    public function restoreToken(LoginRequest $request, RestoreTokenAction $action): JsonResponse
    {
        return response()->json([
            'restore_token' => $action($request),
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  RestoreRequest  $request
     * @param  string  $token
     * @param  RestoreAction  $action
     * @return Response
     */
    public function restore(RestoreRequest $request, string $token, RestoreAction $action): Response
    {
        $action($request, $token);

        return response()->noContent();
    }
}
