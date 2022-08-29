<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    private const API_URL = 'api/users';

    /**
     * レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_success()
    {
        $users = User::factory(11)->create([
            'deleted_at' => null,
        ]);

        $total = $users->count();
        $perPage = config('const.PER_PAGE');
        $lastPage = ceil($total / $perPage);
        $expected = $users->chunk($perPage)[0]->values()->toArray();

        $response = $this->post(self::API_URL);

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
     * @return void
     */
    public function test_paginate_next_page()
    {
        $users = User::factory(21)->create([
            'deleted_at' => null,
        ]);

        $total = $users->count();
        $perPage = config('const.PER_PAGE');
        $lastPage = ceil($total / $perPage);
        $expected = $users->chunk($perPage)[1]->values()->toArray();

        $response = $this->post(self::API_URL . '?page=2');

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
     * @return void
     */
    public function test_paginate_last_page()
    {
        $users = User::factory(11)->create([
            'deleted_at' => null,
        ]);

        $total = $users->count();
        $perPage = config('const.PER_PAGE');
        $lastPage = ceil($total / $perPage);
        $firstItem = $perPage * ($lastPage - 1) + 1;
        $expected = $users->chunk($perPage)[$lastPage - 1]->values()->toArray();

        $response = $this->post(self::API_URL . '?page=' . $lastPage);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $lastPage,
                'last_page' => $lastPage,
                'first_item' => $firstItem,
                'last_item' => $total,
                'has_more_pages' => false,
                'data' => $expected
            ]);
    }

    /**
     * 1ページに表示する件数が変えられるかを確認する
     *
     * @return void
     */
    public function test_paginate_per_page()
    {
        $users = User::factory(16)->create([
            'deleted_at' => null,
        ]);

        $total = $users->count();
        $perPage = 15;
        $lastPage = ceil($total / $perPage);
        $expected = $users->chunk($perPage)[0]->values()->toArray();

        $response = $this->post(self::API_URL, [
            'per_page' => $perPage
        ]);

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

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
