<?php

namespace App\Services\Common;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Scout\Builder as ScoutBuilder;

class PaginateService
{
    /**
     * 通常ページングのレスポンスを生成する
     *
     * @param  Builder|ScoutBuilder  $builder
     * @param  int  $perPage
     * @param  string|null  $order_by
     * @param  string|null  $order
     * @param  array|null  $load
     * @return array
     */
    public function paginate(
        Builder|ScoutBuilder $builder,
        int $perPage,
        string $order_by = null,
        string $order = null,
        array $load = null,
    ): array {
        if ($order_by && $order) {
            $builder->orderBy($order_by, $order)
                ->orderBy('id', $order);
        }

        $data = $builder->paginate($perPage);
        if ($load) {
            $data->load($load);
        }

        return [
            'total' => $data->total(),
            'per_page' => $data->perPage(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'first_item' => $data->firstItem(),
            'last_item' => $data->lastItem(),
            'has_more_pages' => $data->hasMorePages(),
            'items' => $data,
        ];
    }

    /**
     * カーソルページングのレスポンスを生成する
     *
     * @param  Builder  $builder
     * @param  int  $perPage
     * @param  string|null  $order_by
     * @param  string|null  $order
     * @return array
     */
    public function cursorPaginate(
        Builder $builder,
        int $perPage,
        string $order_by = null,
        string $order = null
    ): array {
        $total = $builder->count();

        if ($order_by && $order) {
            $builder->orderBy($order_by, $order)
                ->orderBy('id', $order);
        }

        $data = $builder->cursorPaginate($perPage);

        return [
            'total' => $total,
            'next_cursor' => $this->getCursor($data->nextPageUrl()),
            'prev_cursor' => $this->getCursor($data->previousPageUrl()),
            'items' => $data->items(),
        ];
    }

    /**
     * @param  string|null  $url
     * @return string|null
     */
    private function getCursor(string|null $url): string|null
    {
        if ($url) {
            parse_str(parse_url($url, PHP_URL_QUERY), $query);

            return $query['cursor'];
        }

        return null;
    }
}
