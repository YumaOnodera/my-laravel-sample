<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

class DeleteTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    private const API_URL = 'api/users';

    /**
     * 対象データが論理削除され、レスポンスが想定通りであることを確認する
     *
     * @return void
     * @throws Throwable
     */
    public function test_success()
    {
        User::factory()->create([
            'deleted_at' => null
        ]);

        $response = $this->delete(self::API_URL . '/' . 1);

        $response->assertStatus(200);

        $actualUser = User::withTrashed()->where('id', 1)->first();

        // 論理削除されているか確認する
        $this->assertNotNull($actualUser->deleted_at);

        $actual = $response->decodeResponseJson();

        // レスポンスの値が合っているか確認する
        $this->assertSame('処理に成功しました。', $actual['message']);
    }

    /**
     * 存在しないデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $response = $this->delete(self::API_URL . '/' . 1);

        $response->assertStatus(404);
    }

    /**
     * 論理削除されたデータを指定した時、対象データが更新されず、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_delete_soft_delete_data()
    {
        User::factory()->create([
            'deleted_at' => now()
        ]);

        $response = $this->delete(self::API_URL . '/' . 1);

        $response->assertStatus(404);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
