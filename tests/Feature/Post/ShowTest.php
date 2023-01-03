<?php

namespace Tests\Feature\Post;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/posts';

    /**
     * 未ログインで実行した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_not_logged_in_can_view_data()
    {
        $users = User::factory(10)->create();
        $post = Post::factory()->create();
        $comments = Comment::factory(2)->create();

        $response = $this->get(self::API_URL.'/'.$post->id);

        $post->comments = $comments
            ->where('post_id', $post->id)
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->values()
            ->toArray();
        $post->created_by = $users->find($post->user_id)->name;

        $response
            ->assertStatus(200)
            ->assertExactJson($post->toArray());
    }

    /**
     * レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_can_view_data()
    {
        $users = User::factory(10)->create();
        $post = Post::factory()->create();
        $comments = Comment::factory(2)->create();

        $response = $this->actingAs($users->first())->get(self::API_URL.'/'.$post->id);

        $post->comments = $comments
            ->where('post_id', $post->id)
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->values()
            ->toArray();
        $post->created_by = $users->find($post->user_id)->name;

        $response
            ->assertStatus(200)
            ->assertExactJson($post->toArray());
    }

    /**
     * 存在しないデータを指定した時、参照できないことを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(self::API_URL.'/'. 1);

        $response->assertStatus(404);
    }

    /**
     * 削除ユーザーのデータを指定した時、参照できないことを確認する
     *
     * @return void
     */
    public function test_can_not_view_data_by_deleted_user()
    {
        $user = User::factory()->create();
        $deletedUser = User::factory()->create([
            'deleted_at' => now(),
        ]);
        $post = Post::factory()->create([
            'user_id' => $deletedUser->id,
        ]);

        $response = $this->actingAs($user)->get(self::API_URL.'/'.$post->id);

        $response->assertStatus(404);
    }
}
