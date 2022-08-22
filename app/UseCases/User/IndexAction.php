<?php

namespace App\UseCases\User;

use App\Http\Requests\User\IndexRequest;
use App\Models\User;
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
        $users = User::withTrashed();

        return $this->paginateService->paginate($users, $request->perPage());
    }
}
