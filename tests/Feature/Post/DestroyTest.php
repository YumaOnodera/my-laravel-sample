<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/posts';

    /**
     * 対象データが削除されることを確認する
     *
     * @return void
     */
    public function test_can_delete_data()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->delete(self::API_URL.'/'.$post->id);

        $isExists = Post::where('id', $post->id)->exists();

        // 対象データが削除されていることを確認する
        $this->assertFalse($isExists);

        $response->assertStatus(204);
    }

    /**
     * 他のユーザーの投稿を対象にできないことを確認する
     *
     * @return void
     */
    public function test_can_not_delete_other_users_data()
    {
        $requestUser = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($requestUser)->delete(self::API_URL.'/'.$post->id);

        $response->assertStatus(403);
    }

    /**
     * 存在しないデータを指定した時、実行できないことを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(self::API_URL.'/'. 1);

        $response->assertStatus(422);
    }
}
