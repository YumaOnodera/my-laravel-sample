<?php

namespace Tests\Feature\User;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 対象データが送信した値で更新されることを確認する
     * 管理者ユーザーが実行した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_admin_user_can_update_data()
    {
        $user = User::factory()->create([
            'is_admin' => 1
        ]);
        $name = Factory::create('ja_JP')->name();

        $response = $this->actingAs($user)->put(self::API_URL, [
            'name' => $name
        ]);

        $user->name = $name;

        $afterUpdate = User::where('id', $user->id)->first();

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($user, $afterUpdate);

        $response
            ->assertStatus(200)
            ->assertExactJson($afterUpdate->toArray());
    }

    /**
     * 対象データが送信した値で更新されることを確認する
     * 一般ユーザーが実行した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_general_user_can_update_data()
    {
        $user = User::factory()->create();
        $name = Factory::create('ja_JP')->name();

        $response = $this->actingAs($user)->put(self::API_URL, [
            'name' => $name
        ]);

        $user->name = $name;

        $afterUpdate = User::where('id', $user->id)->first();

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($user, $afterUpdate);

        $filteredAfterUpdate = $afterUpdate->only('id', 'name');

        $response
            ->assertStatus(200)
            ->assertExactJson($filteredAfterUpdate);
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
        $this->assertSame($expected->is_admin, $actual->is_admin);
        $this->assertSame((string) $expected->created_at, (string) $actual->created_at);
        $this->assertSame((string) $expected->deleted_at, (string) $actual->deleted_at);
    }
}
