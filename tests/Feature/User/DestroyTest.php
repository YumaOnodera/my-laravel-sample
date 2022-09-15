<?php

namespace Tests\Feature\User;

use App\Mail\User\Destroy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 対象データが論理削除されることを確認する
     *
     * @return void
     */
    public function test_can_delete_data()
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(self::API_URL . '/' . $user->id);

        $afterUpdate = User::withTrashed()->where('id', $user->id)->first();

        $response->assertStatus(204);

        // 対象データが論理削除されているか確認する
        $this->assertNotNull($afterUpdate->deleted_at);

        Mail::assertSent(Destroy::class, static function ($mail) use ($user, $afterUpdate) {
            return $mail->hasTo($user->email) && $mail->hasCc($afterUpdate->email);
        });
    }

    /**
     * 管理者ユーザーが他のユーザーを対象にできることを確認する
     *
     * @return void
     */
    public function test_admin_user_can_delete_other_data()
    {
        Mail::fake();

        $requestUser = User::factory()->create([
            'is_admin' => 1
        ]);

        $otherUser = User::factory()->create();

        $response = $this->actingAs($requestUser)->delete(self::API_URL . '/' . $otherUser->id);

        $afterUpdate = User::withTrashed()->where('id', $otherUser->id)->first();

        $response->assertStatus(204);

        // 対象データが論理削除されているか確認する
        $this->assertNotNull($afterUpdate->deleted_at);

        Mail::assertSent(Destroy::class, static function ($mail) use ($requestUser, $afterUpdate) {
            return $mail->hasTo($requestUser->email) && $mail->hasCc($afterUpdate->email);
        });
    }

    /**
     * 一般ユーザーが他のユーザーを対象にできないことを確認する
     *
     * @return void
     */
    public function test_general_user_can_not_delete_other_data()
    {
        $requestUser = User::factory()->create();

        $otherUser = User::factory()->create();

        $response = $this->actingAs($requestUser)->delete(self::API_URL . '/' . $otherUser->id);

        $response->assertStatus(403);
    }

    /**
     * 存在しないデータを指定した時、実行できないことを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $user = User::factory()->create([
            'is_admin' => 1
        ]);

        $response = $this->actingAs($user)->delete(self::API_URL . '/' . 2);

        $response->assertStatus(422);
    }

    /**
     * 論理削除されたデータを指定した時、実行できないことを確認する
     *
     * @return void
     */
    public function test_can_not_delete_soft_delete_data()
    {
        $requestUser = User::factory()->create([
            'is_admin' => 1
        ]);

        $otherUser = User::factory()->create([
            'deleted_at' => now()
        ]);

        $response = $this->actingAs($requestUser)->delete(self::API_URL . '/' . $otherUser->id);

        $response->assertStatus(422);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
