<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\IndexRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\PostResource;
use App\UseCases\Post\IndexAction;
use App\UseCases\Post\StoreAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action)
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
     * @return PostResource
     */
    public function store(StoreRequest $request, StoreAction $action)
    {
        return new PostResource($action($request));
    }

    /**
     * Display the specified resource.
     *
     * @return PostResource
     */
    public function show()
    {
        return new PostResource([]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return PostResource
     */
    public function update()
    {
        return new PostResource([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy()
    {
        return response()->noContent();
    }
}
