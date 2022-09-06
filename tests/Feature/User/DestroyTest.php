<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 対象データが論理削除され、レスポンスが想定通りであることを確認する
     *
     * @return void
     * @throws Throwable
     */
    public function test_success()
    {
        $user = User::factory()->create([
            'deleted_at' => null
        ]);

        $response = $this->actingAs($user)->delete(self::API_URL . '/' . 1);

        $afterUpdate = User::withTrashed()->where('id', 1)->first();

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'message' => '処理に成功しました。'
            ]);

        // 対象データが論理削除されているか確認する
        $this->assertNotNull($afterUpdate->deleted_at);
    }

    /**
     * 存在しないデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(self::API_URL . '/' . 2);

        $response->assertStatus(422);
    }

    /**
     * 論理削除されたデータを指定した時、対象データが更新されず、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_destroy_soft_delete_data()
    {
        $user = User::factory()->create();

        User::factory()->create([
            'deleted_at' => now()
        ]);

        $response = $this->actingAs($user)->delete(self::API_URL . '/' . 2);

        $afterUpdate = User::withTrashed()->where('id', 2)->first();

        $response->assertStatus(422);

        // 対象データが論理削除されたままか確認する
        $this->assertNotNull($afterUpdate->deleted_at);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
