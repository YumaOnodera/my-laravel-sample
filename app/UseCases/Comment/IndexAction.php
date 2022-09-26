<?php

namespace App\UseCases\Comment;

use App\Http\Requests\Comment\IndexRequest;
use App\Models\Comment;
use App\Services\Common\PaginateService;

class IndexAction
{
    /**
     * @var PaginateService
     */
    private PaginateService $paginateService;

    /**
     * @param PaginateService $paginateService
     */
    public function __construct(PaginateService $paginateService)
    {
        $this->paginateService = $paginateService;
    }

    /**
     * @param IndexRequest $request
     * @return array
     */
    public function __invoke(IndexRequest $request): array
    {
        $comments = Comment::with('user')
            ->where('post_id', $request->post_id);

        return $this->paginateService->cursorPaginate(
            $comments,
            $request->perPage(),
            $request->order_by,
            $request->order
        );
    }
}
