<?php

namespace App\Services\Common;

use Illuminate\Database\Eloquent\Builder;

class PaginateService
{
    /**
     * 通常ページングのレスポンスを生成する
     *
     * @param Builder $builder
     * @param int $perPage
     * @param string|null $order_by
     * @param string|null $order
     * @return array
     */
    public function paginate(
        Builder $builder,
        int $perPage,
        string $order_by = null,
        string $order = null
    ): array {
        if ($order_by && $order) {
            $builder->orderBy($order_by, $order)
                ->orderBy('id', $order);
        }

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

    /**
     * カーソルページングのレスポンスを生成する
     *
     * @param Builder $builder
     * @param int $perPage
     * @param string|null $order_by
     * @param string|null $order
     * @return array
     */
    public function cursorPaginate(
        Builder $builder,
        int $perPage,
        string $order_by = null,
        string $order = null
    ): array {
        if ($order_by && $order) {
            $builder->orderBy($order_by, $order)
                ->orderBy('id', $order);
        }

        $data = $builder->cursorPaginate($perPage);

        return [
            'total' => $builder->count(),
            'next_cursor' => $data->nextCursor(),
            'prev_cursor' => $data->previousCursor(),
            'items' => $data->items(),
        ];
    }
}
