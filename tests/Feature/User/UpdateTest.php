<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
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

        $request = [
            'name' => '田中二郎'
        ];
        $response = $this->put(self::API_URL . '/' . 1, $request);

        $expected->name = $request['name'];
        $afterUpdate = User::where('id', 1)->first();

        $response
            ->assertStatus(200)
            ->assertExactJson($afterUpdate->toArray());

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($expected, $afterUpdate);
    }

    /**
     * 存在しないデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $response = $this->put(self::API_URL . '/' . 1, [
            'name' => '田中二郎'
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

        $beforeUpdate = User::withTrashed()->where('id', 1)->first();

        $response = $this->put(self::API_URL . '/' . 1, [
            'name' => '田中二郎'
        ]);

        $afterUpdate = User::withTrashed()->where('id', 1)->first();

        $response->assertStatus(422);

        // 対象データが更新されていないことを確認する
        $this->assertSameData($beforeUpdate, $afterUpdate);
    }

    /**
     * 2つのModelの値が同じ値であることを確認する
     *
     * @param Model $expected
     * @param Model $actual
     * @return void
     */
    public function assertSameData (Model $expected, Model $actual)
    {
        $this->assertSame($expected->name, $actual->name);
        $this->assertSame($expected->email, $actual->email);
        $this->assertSame((string) $expected->email_verified_at, (string) $actual->email_verified_at);
        $this->assertSame($expected->password, $actual->password);
        $this->assertSame($expected->remember_token, $actual->remember_token);
        $this->assertSame((string) $expected->created_at, (string) $actual->created_at);
        $this->assertSame((string) $expected->deleted_at, (string) $actual->deleted_at);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
