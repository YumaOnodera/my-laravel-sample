<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/posts/store';

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
        $text = Factory::create('ja_JP')->realText();

        $response = $this->actingAs($user)->post(self::API_URL, [
            'text' => $text,
        ]);

        $actual = $response->json();

        $post = Post::where('id', $actual['id'])->first();

        // 対象データが送信した値で作成されていることを確認する
        $this->assertSame($user->id, $post->user_id);
        $this->assertSame($text, $post->text);

        $post->created_by = $user->name;

        $response
            ->assertStatus(201)
            ->assertExactJson($post->toArray());
    }
}
