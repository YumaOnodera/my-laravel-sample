<?php

namespace Tests;

use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class IndexTestCase extends TestCase
{
    /**
     * レスポンスに必要な要素が含まれていることを確認する
     *
     * @param string $apiUrl
     * @param array $request
     * @param array $expected
     */
    protected function assertDataStructure(string $apiUrl, array $request = [], array $expected = [])
    {
        $response = $this->post($apiUrl, $request);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $expected
            ]);
    }

    /**
     * 最初のページのレスポンスが想定通りであることを確認する
     *
     * @param string $apiUrl
     * @param Collection $data
     * @param array $request
     * @param array $expected
     */
    protected function assertPaginateFirstPage(string $apiUrl, Collection $data, array $request = [], array $expected = [])
    {
        $total = $data->count();
        $perPage = config('const.PER_PAGE');

        if ($total <= ($perPage * 2)) {
            throw new RuntimeException('テストデータは3ページ分以上用意してください。');
        }

        $lastPage = ceil($total / $perPage);
        if (empty($expected)) {
            $expected = $data->chunk($perPage)[0]->values()->toArray();
        }

        $response = $this->post($apiUrl, $request);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => 1,
                'last_page' => $lastPage,
                'first_item' => 1,
                'last_item' => $perPage,
                'has_more_pages' => true,
                'data' => $expected
            ]);
    }

    /**
     * 次のページのレスポンスが想定通りであることを確認する
     *
     * @param string $apiUrl
     * @param Collection $data
     * @param array $request
     * @param array $expected
     */
    protected function assertPaginateNextPage(string $apiUrl, Collection $data, array $request = [], array $expected = [])
    {
        $total = $data->count();
        $perPage = config('const.PER_PAGE');

        if ($total <= ($perPage * 2)) {
            throw new RuntimeException('テストデータは3ページ分以上用意してください。');
        }

        $lastPage = ceil($total / $perPage);
        if (empty($expected)) {
            $expected = $data->chunk($perPage)[1]->values()->toArray();
        }

        $response = $this->post($apiUrl . '?page=2', $request);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => 2,
                'last_page' => $lastPage,
                'first_item' => $perPage + 1,
                'last_item' => $perPage * 2,
                'has_more_pages' => true,
                'data' => $expected
            ]);
    }

    /**
     * 最後のページのレスポンスが想定通りであることを確認する
     *
     * @param string $apiUrl
     * @param Collection $data
     * @param array $request
     * @param array $expected
     */
    protected function assertPaginateLastPage(string $apiUrl, Collection $data, array $request = [], array $expected = [])
    {
        $total = $data->count();
        $perPage = config('const.PER_PAGE');

        if ($total <= ($perPage * 2)) {
            throw new RuntimeException('テストデータは3ページ分以上用意してください。');
        }

        $lastPage = ceil($total / $perPage);
        $offset = $perPage * ($lastPage - 1);
        if (empty($expected)) {
            $expected = $data->chunk($perPage)[$lastPage - 1]->values()->toArray();
        }

        $response = $this->post($apiUrl . '?page=' . $lastPage, $request);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $lastPage,
                'last_page' => $lastPage,
                'first_item' => $offset + 1,
                'last_item' => $total,
                'has_more_pages' => false,
                'data' => $expected
            ]);
    }

    /**
     * 1ページに表示する件数が変えられるかを確認する
     *
     * @param string $apiUrl
     * @param Collection $data
     * @param array $request
     * @param array $expected
     */
    protected function assertPaginatePerPage(string $apiUrl, Collection $data, array $request = [], array $expected = [])
    {
        $total = $data->count();
        $perPage = 15;

        if ($total <= ($perPage * 2)) {
            throw new RuntimeException('テストデータは3ページ分以上用意してください。');
        }

        $lastPage = ceil($total / $perPage);
        if (empty($request)) {
            $request = [
                'per_page' => $perPage
            ];
        }
        if (empty($expected)) {
            $expected = $data->chunk($perPage)[0]->values()->toArray();
        }

        $response = $this->post($apiUrl, $request);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => 1,
                'last_page' => $lastPage,
                'first_item' => 1,
                'last_item' => $perPage,
                'has_more_pages' => true,
                'data' => $expected
            ]);
    }
}
