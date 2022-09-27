<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/posts';

    /**
     * レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_can_view_data()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->get(self::API_URL.'/'.$post->id);

        $post->created_by = $user->name;

        $response
            ->assertStatus(200)
            ->assertExactJson($post->toArray());
    }

    /**
     * 存在しないデータを指定した時、実行できないことを確認する
     *
     * @return void
     */
    public function test_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(self::API_URL.'/'. 1);

        $response->assertStatus(404);
    }
}
