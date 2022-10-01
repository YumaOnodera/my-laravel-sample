<?php

namespace App\UseCases\Post;

use App\Http\Requests\Post\IndexRequest;
use App\Models\Post;
use App\Services\Common\PaginateService;

class IndexAction
{
    /**
     * @var PaginateService
     */
    private PaginateService $paginateService;

    /**
     * @param  PaginateService  $paginateService
     */
    public function __construct(PaginateService $paginateService)
    {
        $this->paginateService = $paginateService;
    }

    /**
     * @param  IndexRequest  $request
     * @return array
     */
    public function __invoke(IndexRequest $request): array
    {
        if ($request->keyword) {
            $posts = Post::search($request->keyword);
        } else {
            $posts = Post::with('user');
        }

        if ($request->user_ids) {
            $posts->whereIn('user_id', $request->user_ids);
        }

        return $this->paginateService->paginate(
            $posts,
            $request->perPage(),
            $request->order_by,
            $request->order
        );
    }
}
