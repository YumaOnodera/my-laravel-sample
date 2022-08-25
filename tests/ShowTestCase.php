<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;

class ShowTestCase extends TestCase
{
    /**
     * 存在するデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @param string $apiUrl
     * @param Model $expected
     * @param int $id
     */
    protected function assertRequestData(string $apiUrl, Model $expected, int $id = 1)
    {
        $response = $this->get($apiUrl . '/' . $id);

        $response
            ->assertStatus(200)
            ->assertExactJson($expected->toArray());
    }

    /**
     * 存在しないデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @param string $apiUrl
     * @param int $id
     */
    protected function assertRequestNotFoundData(string $apiUrl, int $id = 0)
    {
        $response = $this->get($apiUrl . '/' . $id);

        $response->assertStatus(404);
    }

    /**
     * 論理削除されたデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @param string $apiUrl
     * @param Model $expected
     * @param bool $withTrashed
     * @param int $id
     */
    protected function assertRequestSoftDeleteData(
        string $apiUrl,
        Model $expected,
        bool $withTrashed = false,
        int $id = 1
    ) {
        $response = $this->get($apiUrl . '/' . $id);

        if ($withTrashed) {
            $response
                ->assertStatus(200)
                ->assertExactJson($expected->toArray());
        } else {
            $response->assertStatus(404);
        }
    }
}
