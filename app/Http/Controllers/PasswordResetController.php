<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordReset\UpdateRequest;
use App\UseCases\PasswordReset\UpdateAction;
use Illuminate\Http\Response;

class PasswordResetController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  UpdateAction  $action
     * @return Response
     */
    public function update(UpdateRequest $request, UpdateAction $action): Response
    {
        $action($request);

        return response()->noContent();
    }
}
