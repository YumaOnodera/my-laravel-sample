<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return PostResource
     */
    public function store()
    {
        return new PostResource([]);
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
