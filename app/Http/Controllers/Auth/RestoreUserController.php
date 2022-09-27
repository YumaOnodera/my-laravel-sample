<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\UseCases\Auth\RestoreAction;
use Illuminate\Http\Response;

class RestoreUserController extends Controller
{
    /**
     * Restore the specified resource from storage.
     *
     * @param  LoginRequest  $request
     * @param  RestoreAction  $action
     * @return Response
     */
    public function restore(LoginRequest $request, RestoreAction $action): Response
    {
        $action($request);

        return response()->noContent();
    }
}
