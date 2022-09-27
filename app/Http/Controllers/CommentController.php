<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\DestroyRequest;
use App\Http\Requests\Comment\IndexRequest;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Resources\CommentResource;
use App\UseCases\Comment\DestroyAction;
use App\UseCases\Comment\IndexAction;
use App\UseCases\Comment\StoreAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $comments = $action($request);

        return response()->json([
            'total' => $comments['total'],
            'next_cursor' => $comments['next_cursor'],
            'prev_cursor' => $comments['prev_cursor'],
            'data' => CommentResource::collection($comments['items']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return CommentResource
     */
    public function store(StoreRequest $request, StoreAction $action): CommentResource
    {
        return new CommentResource($action($request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  DestroyRequest  $request
     * @param  int  $id
     * @param  DestroyAction  $action
     * @return Response
     */
    public function destroy(DestroyRequest $request, int $id, DestroyAction $action): Response
    {
        $action($request, $id);

        return response()->noContent();
    }
}
