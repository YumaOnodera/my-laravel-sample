<?php

namespace Tests\Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/comments/store';

    /**
     * 対象データが送信した値で作成されることを確認する
     *
     * @return void
     *
     * @throws Throwable
     */
    public function test_can_store_data()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $text = Factory::create('ja_JP')->realText();

        $response = $this->actingAs($user)->post(self::API_URL, [
            'post_id' => $post->id,
            'text' => $text,
        ]);

        $actual = $response->json();

        $comment = Comment::where('id', $actual['id'])->first();

        // 対象データが送信した値で作成されていることを確認する
        $this->assertSame($user->id, $comment->user_id);
        $this->assertSame($post->id, $comment->post_id);
        $this->assertSame($text, $comment->text);

        $comment->created_by = $user->name;

        $response
            ->assertStatus(201)
            ->assertExactJson($comment->toArray());
    }
}
