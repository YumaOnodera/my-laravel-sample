<?php

namespace Tests\Feature\User;

use App\Mail\Auth\Restore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RestoreTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 管理者ユーザーが実行した時、対象データが復活することを確認する
     *
     * @return void
     */
    public function test_admin_user_can_restore_other_data()
    {
        Mail::fake();

        $requestUser = User::factory()->create([
            'is_admin' => 1
        ]);
        $otherUser = User::factory()->create([
            'deleted_at' => now()
        ]);

        $uri = sprintf('%s/%s/%s', self::API_URL, $otherUser->id, 'restore');
        $response = $this->actingAs($requestUser)->post($uri);

        $afterUpdate = User::where('id', $otherUser->id)->first();

        $response->assertStatus(204);

        // 対象データが復活しているか確認する
        $this->assertNull($afterUpdate->deleted_at);

        Mail::assertSent(Restore::class, static function ($mail) use ($requestUser, $otherUser) {
            return $mail->hasTo($requestUser->email) && $mail->hasCc($otherUser->email);
        });
    }

    /**
     * 一般ユーザーが実行できないことを確認する
     *
     * @return void
     */
    public function test_general_user_can_not_restore_data()
    {
        $requestUser = User::factory()->create();
        $otherUser = User::factory()->create([
            'deleted_at' => now()
        ]);

        $uri = sprintf('%s/%s/%s', self::API_URL, $otherUser->id, 'restore');
        $response = $this->actingAs($requestUser)->post($uri);

        $response->assertStatus(403);
    }

    /**
     * 存在しないデータを指定した時、実行できないことを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $requestUser = User::factory()->create([
            'is_admin' => 1
        ]);

        $uri = sprintf('%s/%s/%s', self::API_URL, 2, 'restore');
        $response = $this->actingAs($requestUser)->post($uri);

        $response->assertStatus(422);
    }

    /**
     * 論理削除されていないデータを指定した時、実行できないことを確認する
     *
     * @return void
     */
    public function test_can_not_restore_not_soft_delete_data()
    {
        $requestUser = User::factory()->create([
            'is_admin' => 1
        ]);
        $otherUser = User::factory()->create();

        $uri = sprintf('%s/%s/%s', self::API_URL, $otherUser->id, 'restore');
        $response = $this->actingAs($requestUser)->post($uri);

        $response->assertStatus(422);
    }
}
