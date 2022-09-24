<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\DestroyRequest;
use App\Http\Requests\Post\IndexRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Resources\PostResource;
use App\UseCases\Post\DestroyAction;
use App\UseCases\Post\IndexAction;
use App\UseCases\Post\ShowAction;
use App\UseCases\Post\StoreAction;
use App\UseCases\Post\UpdateAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostController extends Controller
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
        $posts = $action($request);

        return response()->json([
            'total' => $posts['total'],
            'per_page' => $posts['per_page'],
            'current_page' => $posts['current_page'],
            'last_page' => $posts['last_page'],
            'first_item' => $posts['first_item'],
            'last_item' => $posts['last_item'],
            'has_more_pages' => $posts['has_more_pages'],
            'data' => PostResource::collection($posts['items']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @param StoreAction $action
     * @return PostResource
     */
    public function store(StoreRequest $request, StoreAction $action): PostResource
    {
        return new PostResource($action($request));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param ShowAction $action
     * @return PostResource
     */
    public function show(int $id, ShowAction $action): PostResource
    {
        return new PostResource($action($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int $id
     * @param UpdateAction $action
     * @return PostResource
     */
    public function update(UpdateRequest $request, int $id, UpdateAction $action): PostResource
    {
        return new PostResource($action($request, $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $request
     * @param int $id
     * @param DestroyAction $action
     * @return Response
     */
    public function destroy(DestroyRequest $request, int $id, DestroyAction $action): Response
    {
        $action($request, $id);

        return response()->noContent();
    }
}
