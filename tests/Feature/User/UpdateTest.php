<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    private const API_URL = 'api/users';

    /**
     * 対象データが送信した値で更新され、レスポンスが想定通りであることを確認する
     *
     * @return void
     * @throws Throwable
     */
    public function test_success()
    {
        $expected = User::factory()->create([
            'deleted_at' => null,
            'name' => '山田一郎'
        ]);
        $expected->name = 'テスト太郎';

        $response = $this->put(self::API_URL . '/' . 1, [
            'name' => 'テスト太郎'
        ]);

        $actual = $response->decodeResponseJson();

        $updatedUser = User::where('id', 1)->first();

        foreach ($updatedUser->toArray() as $key => $item) {
            switch ($key) {
                case 'email_verified_at':
                case 'created_at':
                case 'updated_at':
                case 'deleted_at':
                    continue 2;
            }

            // 更新後のレコードの値が合っているか確認する
            $this->assertSame($expected[$key], $item);

            // レスポンスの値が合っているか確認する
            $this->assertSame($item, $actual[$key]);
        }
    }

    /**
     * 存在しないデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $response = $this->put(self::API_URL . '/' . 1, [
            'name' => 'テスト太郎'
        ]);

        $response->assertStatus(422);
    }

    /**
     * 論理削除されたデータを指定した時、対象データが更新されず、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_update_soft_delete_data()
    {
        User::factory()->create([
            'deleted_at' => now(),
            'name' => '山田一郎'
        ]);

        $expected = User::withTrashed()->where('id', 1)->first();

        $response = $this->put(self::API_URL . '/' . 1, [
            'name' => 'テスト太郎'
        ]);

        $actual = User::withTrashed()->where('id', 1)->first();

        foreach ($actual->toArray() as $key => $actualValue) {
            switch ($key) {
                case 'email_verified_at':
                case 'created_at':
                case 'updated_at':
                case 'deleted_at':
                    continue 2;
            }

            // 対象データが更新されていないことを確認する
            $this->assertSame($expected[$key], $actualValue);
        }

        $response->assertStatus(422);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
