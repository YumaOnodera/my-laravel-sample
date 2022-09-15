<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\DestroyRequest;
use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\RestoreRequest;
use App\Http\Requests\User\UpdateEmailRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\UseCases\User\DestroyAction;
use App\UseCases\User\IndexAction;
use App\UseCases\User\RestoreAction;
use App\UseCases\User\ShowAction;
use App\UseCases\User\UpdateAction;
use App\UseCases\User\UpdateEmailAction;
use App\UseCases\User\UpdatePasswordAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @param IndexAction $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $users = $action($request);

        return response()->json([
            'total' => $users['total'],
            'per_page' => $users['per_page'],
            'current_page' => $users['current_page'],
            'last_page' => $users['last_page'],
            'first_item' => $users['first_item'],
            'last_item' => $users['last_item'],
            'has_more_pages' => $users['has_more_pages'],
            'data' => UserResource::collection($users['items']),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param ShowAction $action
     * @return UserResource
     */
    public function show(int $id, ShowAction $action): UserResource
    {
        return new UserResource($action($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param UpdateAction $action
     * @return UserResource
     */
    public function update(UpdateRequest $request, UpdateAction $action): UserResource
    {
        return new UserResource($action($request));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateEmailRequest $request
     * @param UpdateEmailAction $action
     * @return Response
     */
    public function updateEmail(UpdateEmailRequest $request, UpdateEmailAction $action): Response
    {
        $action($request);

        return response()->noContent();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePasswordRequest $request
     * @param UpdatePasswordAction $action
     * @return Response
     */
    public function updatePassword(UpdatePasswordRequest $request, UpdatePasswordAction $action): Response
    {
        $action($request);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $request
     * @param int $id
     * @param DestroyAction $action
     * @return Response
     */
    public function destroy(DestroyRequest $request, int $id, DestroyAction $action)
    {
        $action($request, $id);

        return response()->noContent();
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param RestoreRequest $request
     * @param int $id
     * @param RestoreAction $action
     * @return Response
     */
    public function restore(RestoreRequest $request, int $id, RestoreAction $action)
    {
        $action($request, $id);

        return response()->noContent();
    }
}
