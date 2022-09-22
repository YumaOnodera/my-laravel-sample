<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MySelfTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/user';

    /**
     * 管理者ユーザーが実行した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_admin_user_can_view_data()
    {
        $user = User::factory()->create([
            'is_admin' => 1
        ]);

        $response = $this->actingAs($user)->get(self::API_URL);

        $response
            ->assertStatus(200)
            ->assertExactJson($user->toArray());
    }

    /**
     * 一般ユーザーが実行した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_general_user_can_view_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(self::API_URL);

        $filteredUser = $user->only(['id', 'name']);

        $response
            ->assertStatus(200)
            ->assertExactJson($filteredUser);
    }
}
