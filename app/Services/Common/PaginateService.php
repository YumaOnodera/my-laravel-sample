<?php

namespace App\Services\Common;

use Illuminate\Database\Eloquent\Builder;

class PaginateService
{
    /**
     * ページネーションのレスポンスを返却する
     *
     * @param Builder $builder
     * @param int $perPage
     * @return array
     */
    public function paginate(Builder $builder, int $perPage): array
    {
        $data = $builder->paginate($perPage);

        return [
            'total' => $data->total(),
            'per_page' => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'first_item' => $data->firstItem(),
            'last_item' => $data->lastItem(),
            'has_more_pages' => $data->hasMorePages(),
            'items' => $data->items(),
        ];
    }
}
