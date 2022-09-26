<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\IndexRequest;
use App\Http\Resources\CommentResource;
use App\UseCases\Comment\IndexAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
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
     * @param Request $request
     * @return CommentResource
     */
    public function store(Request $request): CommentResource
    {
        return new CommentResource([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(): Response
    {
        return response()->noContent();
    }
}
