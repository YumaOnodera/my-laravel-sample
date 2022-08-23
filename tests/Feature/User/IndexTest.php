<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\IndexTestCase;

class IndexTest extends IndexTestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();

        $this->apiUrl = 'api/users';
        $this->users = User::factory(50)->create([
            'deleted_at' => null,
        ]);
    }

    /**
     * レスポンスに必要な要素が含まれていることを確認する
     *
     * @return void
     */
    public function test_data_structure()
    {
        $this->assertDataStructure(
            $this->apiUrl,
            [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ]
            ]
        );
    }

    /**
     * 最初のページのレスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_paginate_first_page()
    {
        $this->assertPaginateFirstPage($this->apiUrl, $this->users);
    }

    /**
     * 次のページのレスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_paginate_next_page()
    {
        $this->assertPaginateNextPage($this->apiUrl, $this->users);
    }

    /**
     * 最後のページのレスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_paginate_last_page()
    {
        $this->assertPaginateLastPage($this->apiUrl, $this->users);
    }

    /**
     * 1ページに表示する件数が変えられるかを確認する
     *
     * @return void
     */
    public function test_paginate_per_page()
    {
        $this->assertPaginatePerPage($this->apiUrl, $this->users);
    }
}
