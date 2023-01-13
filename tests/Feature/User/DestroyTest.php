<?php

namespace Tests\Feature\User;

use App\Mail\User\Destroy;
use App\Models\User;
use App\Models\UserRestore;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $response = $this->actingAs($user)->delete(self::API_URL.'/'.$user->id, [
            'password' => 'password',
        ]);

        $afterUpdate = User::withTrashed()->find($user->id);
        $userRestore = UserRestore::where('user_id', $user->id)->first();

        // 対象データが論理削除されていることを確認する
        $this->assertNotNull($afterUpdate->deleted_at);
        // ユーザー復活用トークンが保存されていることを確認する
        $this->assertSame($user->id, $userRestore->user_id);
        $this->assertNotNull($userRestore->token);

        $response->assertStatus(204);

        Mail::assertSent(Destroy::class, static function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * 管理者ユーザーが他のユーザーを対象にできることを確認する
     *
     * @return void
     */
    public function test_admin_user_can_delete_other_users()
    {
        Mail::fake();

        $requestUser = User::factory()->create([
            'is_admin' => 1,
        ]);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($requestUser)->delete(self::API_URL.'/'.$otherUser->id, [
            'password' => 'password',
        ]);

        $afterUpdate = User::withTrashed()->find($otherUser->id);
        $userRestoreExists = UserRestore::where('user_id', $otherUser->id)->exists();

        // 対象データが論理削除されていることを確認する
        $this->assertNotNull($afterUpdate->deleted_at);
        // ユーザー復活用トークンが保存されていないことを確認する
        $this->assertFalse($userRestoreExists);

        $response->assertStatus(204);

        Mail::assertSent(Destroy::class, static function ($mail) use ($otherUser) {
            return $mail->hasTo($otherUser->email);
        });
    }

    /**
     * 管理者ユーザーが自身を対象にできることを確認する
     *
     * @return void
     */
    public function test_admin_user_can_delete_self()
    {
        Mail::fake();

        $user = User::factory()->create([
            'is_admin' => 1,
        ]);

        $response = $this->actingAs($user)->delete(self::API_URL.'/'.$user->id, [
            'password' => 'password',
        ]);

        $afterUpdate = User::withTrashed()->find($user->id);
        $userRestore = UserRestore::where('user_id', $user->id)->first();

        // 対象データが論理削除されていることを確認する
        $this->assertNotNull($afterUpdate->deleted_at);
        // ユーザー復活用トークンが保存されていることを確認する
        $this->assertSame($user->id, $userRestore->user_id);
        $this->assertNotNull($userRestore->token);

        $response->assertStatus(204);

        Mail::assertSent(Destroy::class, static function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * 一般ユーザーが他のユーザーを対象にできないことを確認する
     *
     * @return void
     */
    public function test_general_user_can_not_delete_other_users()
    {
        $requestUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($requestUser)->delete(self::API_URL.'/'.$otherUser->id, [
            'password' => 'password',
        ]);

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
            'is_admin' => 1,
        ]);

        $response = $this->actingAs($user)->delete(self::API_URL.'/'. 2, [
            'password' => 'password',
        ]);

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
            'is_admin' => 1,
        ]);
        $otherUser = User::factory()->create([
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($requestUser)->delete(self::API_URL.'/'.$otherUser->id, [
            'password' => 'password',
        ]);

        $response->assertStatus(422);
    }

    /**
     * パスワードが異なる時、実行できないことを確認する
     *
     * @return void
     */
    public function test_can_not_delete_with_invalid_password()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(self::API_URL.'/'.$user->id, [
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
    }
}
