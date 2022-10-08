<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RestoreTokenTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'restore-token';

    /**
     * 削除ユーザーでリクエストした時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_deleted_user()
    {
        $user = User::factory()->create([
            'deleted_at' => now(),
            'restore_token' => Str::random(10),
        ]);

        $response = $this->post(self::API_URL, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $actual = $response->json();

        $this->assertNotNull($actual['restore_token']);

        $response->assertStatus(200);
    }

    /**
     * 削除されていないユーザーでリクエストした時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_not_deleted_user()
    {
        $user = User::factory()->create();

        $response = $this->post(self::API_URL, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $actual = $response->json();

        $this->assertNull($actual['restore_token']);

        $response->assertStatus(200);
    }

    /**
     * パスワードが異なる時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_deleted_user_with_invalid_password()
    {
        $user = User::factory()->create([
            'deleted_at' => now(),
        ]);

        $response = $this->post(self::API_URL, [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $actual = $response->json();

        $this->assertNull($actual['restore_token']);

        $response->assertStatus(200);
    }
}
