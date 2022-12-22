<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailReset\StoreRequest;
use App\Http\Requests\EmailReset\UpdateRequest;
use App\UseCases\EmailReset\StoreAction;
use App\UseCases\EmailReset\UpdateAction;
use Illuminate\Http\Response;

class EmailResetController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return Response
     */
    public function store(StoreRequest $request, StoreAction $action): Response
    {
        $action($request);

        return response()->noContent();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  string  $token
     * @param  UpdateAction  $action
     * @return Response
     */
    public function update(UpdateRequest $request, string $token, UpdateAction $action): Response
    {
        $action($request, $token);

        return response()->noContent();
    }
}
