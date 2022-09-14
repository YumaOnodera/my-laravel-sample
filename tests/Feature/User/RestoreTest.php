<?php

namespace Tests\Feature\User;

use App\Mail\Auth\Restore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RestoreTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 対象データが復活し、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_success()
    {
        Mail::fake();

        $requestUser = User::factory()->create([
            'is_admin' => 1
        ]);
        $user = User::factory()->create([
            'deleted_at' => now()
        ]);

        $uri = sprintf('%s/%s/%s', self::API_URL, $user->id, 'restore');
        $response = $this->actingAs($requestUser)->put($uri);

        $afterUpdate = User::where('id', $user->id)->first();

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'message' => '処理に成功しました。'
            ]);

        // 対象データが復活しているか確認する
        $this->assertNull($afterUpdate->deleted_at);

        Mail::assertSent(Restore::class, static function ($mail) use ($requestUser, $user) {
            return $mail->hasTo($requestUser->email) && $mail->hasCc($user->email);
        });
    }

    /**
     * 一般ユーザーが実行できないことを確認
     *
     * @return void
     */
    public function test_general_user_can_not_restore_other_user()
    {
        $requestUser = User::factory()->create();
        $user = User::factory()->create([
            'deleted_at' => now()
        ]);

        $uri = sprintf('%s/%s/%s', self::API_URL, $user->id, 'restore');
        $response = $this->actingAs($requestUser)->put($uri);

        $response->assertStatus(403);
    }

    /**
     * 存在しないデータを指定した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $requestUser = User::factory()->create([
            'is_admin' => 1
        ]);

        $uri = sprintf('%s/%s/%s', self::API_URL, 2, 'restore');
        $response = $this->actingAs($requestUser)->put($uri);

        $response->assertStatus(422);
    }

    /**
     * 論理削除されていないデータを指定した時、対象データが更新されず、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_destroy_soft_delete_data()
    {
        $requestUser = User::factory()->create([
            'is_admin' => 1
        ]);
        $user = User::factory()->create();

        $uri = sprintf('%s/%s/%s', self::API_URL, $user->id, 'restore');
        $response = $this->actingAs($requestUser)->put($uri);

        $afterUpdate = User::withTrashed()->where('id', $user->id)->first();

        $response->assertStatus(422);

        // 対象データが論理削除されていないままか確認する
        $this->assertNull($afterUpdate->deleted_at);
    }

    public function tearDown(): void
    {
        DB::table('users')->truncate();
    }
}
