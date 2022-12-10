<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePermissionTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 管理者ユーザーが実行した時、対象データを管理者に更新できることを確認する
     *
     * @return void
     */
    public function test_admin_user_can_update_data_to_admin()
    {
        $requestUser = User::factory()->create([
            'is_admin' => 1,
        ]);
        $otherUser = User::factory()->create();

        $uri = sprintf('%s/%s/%s', self::API_URL, $otherUser->id, 'update-permission');
        $response = $this->actingAs($requestUser)->put($uri, [
            'is_admin' => true,
        ]);

        $otherUser->is_admin = true;

        $afterUpdate = User::find($otherUser->id);

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($otherUser, $afterUpdate);

        $response->assertStatus(204);
    }

    /**
     * 管理者ユーザーが実行した時、対象データを一般ユーザーに更新できることを確認する
     *
     * @return void
     */
    public function test_admin_user_can_update_data_to_general()
    {
        $requestUser = User::factory()->create([
            'is_admin' => 1,
        ]);
        $otherUser = User::factory()->create([
            'is_admin' => 1,
        ]);

        $uri = sprintf('%s/%s/%s', self::API_URL, $otherUser->id, 'update-permission');
        $response = $this->actingAs($requestUser)->put($uri, [
            'is_admin' => false,
        ]);

        $otherUser->is_admin = false;

        $afterUpdate = User::find($otherUser->id);

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($otherUser, $afterUpdate);

        $response->assertStatus(204);
    }

    /**
     * 一般ユーザーが実行できないことを確認する
     *
     * @return void
     */
    public function test_general_user_can_not_restore_data()
    {
        $requestUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $uri = sprintf('%s/%s/%s', self::API_URL, $otherUser->id, 'update-permission');
        $response = $this->actingAs($requestUser)->put($uri, [
            'is_admin' => true,
        ]);

        $response->assertStatus(403);
    }

    /**
     * 2つのModelの値が同じ値であることを確認する
     *
     * @param  Model  $expected
     * @param  Model  $actual
     * @return void
     */
    public function assertSameData(Model $expected, Model $actual)
    {
        $this->assertSame($expected->name, $actual->name);
        $this->assertSame($expected->email, $actual->email);
        $this->assertSame((string) $expected->email_verified_at, (string) $actual->email_verified_at);
        $this->assertSame($expected->password, $actual->password);
        $this->assertSame($expected->remember_token, $actual->remember_token);
        $this->assertSame($expected->restore_token, $actual->restore_token);
        $this->assertSame($expected->is_admin, $actual->is_admin);
        $this->assertSame((string) $expected->created_at, (string) $actual->created_at);
        $this->assertSame((string) $expected->deleted_at, (string) $actual->deleted_at);
    }
}
