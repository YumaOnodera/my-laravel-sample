<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/posts';

    /**
     * 対象データが送信した値で更新されることを確認する
     *
     * @return void
     */
    public function test_can_update_data()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $text = Factory::create('ja_JP')->realText();

        $response = $this->actingAs($user)->put(self::API_URL.'/'.$post->id, [
            'text' => $text,
        ]);

        $post->text = $text;

        $afterUpdate = Post::where('id', $post->id)->first();

        // 対象データが送信した値で更新されていることを確認する
        $this->assertSameData($post, $afterUpdate);

        $afterUpdate->created_by = $user->name;

        $response
            ->assertStatus(200)
            ->assertExactJson($afterUpdate->toArray());
    }

    /**
     * 他のユーザーの投稿を対象にできないことを確認する
     *
     * @return void
     */
    public function test_can_not_update_other_users_data()
    {
        $requestUser = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $text = Factory::create('ja_JP')->realText();

        $response = $this->actingAs($requestUser)->put(self::API_URL.'/'.$post->id, [
            'text' => $text,
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
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(self::API_URL.'/'. 1);

        $response->assertStatus(422);
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
        $this->assertSame($expected->user_id, $actual->user_id);
        $this->assertSame($expected->text, $actual->text);
        $this->assertSame((string) $expected->created_at, (string) $actual->created_at);
    }
}
