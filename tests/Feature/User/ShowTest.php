<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 存在するデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_success()
    {
        $expected = User::factory()->create([
            'is_admin' => 1
        ]);

        $response = $this->actingAs($expected)->get(self::API_URL . '/' . 1);

        $response
            ->assertStatus(200)
            ->assertExactJson($expected->toArray());
    }

    /**
     * 論理削除されたデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_get_soft_delete_data()
    {
        $user = User::factory()->create([
            'is_admin' => 1
        ]);

        $expected = User::factory()->create([
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(self::API_URL . '/' . 2);

        $response
            ->assertStatus(200)
            ->assertExactJson($expected->toArray());
    }

    /**
     * 一般ユーザーが実行できないことを確認
     *
     * @return void
     */
    public function test_authorization_error_general_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(self::API_URL . '/' . 1);

        $response->assertStatus(403);
    }


    /**
     * 存在しないデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $user = User::factory()->create([
            'is_admin' => 1
        ]);

        $response = $this->actingAs($user)->get(self::API_URL . '/' . 2);

        $response->assertStatus(404);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
