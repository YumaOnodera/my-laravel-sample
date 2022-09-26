<?php

namespace Tests\Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/comments';

    /**
     * レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_can_view_data()
    {
        $users = User::factory(10)->create();
        $post = Post::factory()->create();
        $comments = Comment::factory(16)->create();

        $total = $comments->count();
        $perPage = config('const.PER_PAGE.CURSOR_PAGINATE');
        $expected = $comments
            ->where('post_id', $post->id)
            ->map(function($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;
                return $item->only('id', 'post_id', 'user_id', 'text', 'created_by', 'created_at');
            })
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

        $response = $this->actingAs($users->first())->post(self::API_URL, [
            'post_id' => $post->id
        ]);

        $actual = $response->json();

        $this->assertNotNull($actual['next_cursor']);
        $this->assertNull($actual['prev_cursor']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => $total,
                'data' => $expected
            ]);
    }

    /**
     * 次のページのレスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_can_view_data_next_page()
    {
        $users = User::factory(10)->create();
        $post = Post::factory()->create();
        $comments = Comment::factory(31)->create();

        $total = $comments->count();
        $perPage = config('const.PER_PAGE.CURSOR_PAGINATE');
        $expected = $comments
            ->where('post_id', $post->id)
            ->map(function($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;
                return $item->only('id', 'post_id', 'user_id', 'text', 'created_by', 'created_at');
            })
            ->chunk($perPage)[1]
            ->values()
            ->toArray();

        $response = $this->actingAs($users->first())->post(self::API_URL, [
            'post_id' => $post->id
        ]);
        $nextCursor = $response->json()['next_cursor'];

        $nextResponse = $this
            ->actingAs($users->first())
            ->post(self::API_URL . '?cursor=' . $nextCursor, [
                'post_id' => $post->id
            ]);

        $actual = $nextResponse->json();

        $this->assertNotNull($actual['next_cursor']);
        $this->assertNotNull($actual['prev_cursor']);

        $nextResponse
            ->assertStatus(200)
            ->assertJson([
                'total' => $total,
                'data' => $expected
            ]);
    }

    /**
     * 最後のページのレスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_can_view_data_last_page()
    {
        $users = User::factory(10)->create();
        $post = Post::factory()->create();
        $comments = Comment::factory(16)->create();

        $total = $comments->count();
        $perPage = config('const.PER_PAGE.CURSOR_PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $comments
            ->where('post_id', $post->id)
            ->map(function($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;
                return $item->only('id', 'post_id', 'user_id', 'text', 'created_by', 'created_at');
            })
            ->chunk($perPage)[$lastPage - 1]
            ->values()
            ->toArray();

        $response = $this->actingAs($users->first())->post(self::API_URL, [
            'post_id' => $post->id
        ]);
        $nextCursor = $response->json()['next_cursor'];

        $nextResponse = $this
            ->actingAs($users->first())
            ->post(self::API_URL . '?cursor=' . $nextCursor, [
                'post_id' => $post->id
            ]);

        $actual = $nextResponse->json();

        $this->assertNull($actual['next_cursor']);
        $this->assertNotNull($actual['prev_cursor']);

        $nextResponse
            ->assertStatus(200)
            ->assertJson([
                'total' => $total,
                'data' => $expected
            ]);
    }

    /**
     * 作成日時の昇順で並び替えられるかを確認する
     *
     * @return void
     */
    public function test_can_view_data_with_sort_in_asc_order_of_created_at()
    {
        $users = User::factory(10)->create();
        $post = Post::factory()->create();
        $comments = Comment::factory(16)->create();

        $total = $comments->count();
        $perPage = config('const.PER_PAGE.CURSOR_PAGINATE');
        $expected = $comments
            ->map(function($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;
                return $item->only('id', 'post_id', 'user_id', 'text', 'created_by', 'created_at');
            })
            ->sortBy('created_at')
            ->sortBy('id')
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

        $response = $this->actingAs($users->first())->post(self::API_URL, [
            'post_id' => $post->id,
            'order_by' => 'created_at',
            'order' => 'asc'
        ]);

        $actual = $response->json();

        $this->assertNotNull($actual['next_cursor']);
        $this->assertNull($actual['prev_cursor']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => $total,
                'data' => $expected
            ]);
    }

    /**
     * 作成日時の降順で並び替えられるかを確認する
     *
     * @return void
     */
    public function test_can_view_data_with_sort_in_desc_order_of_created_at()
    {
        $users = User::factory(10)->create();
        $post = Post::factory()->create();
        $comments = Comment::factory(16)->create();

        $total = $comments->count();
        $perPage = config('const.PER_PAGE.CURSOR_PAGINATE');
        $expected = $comments
            ->map(function($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;
                return $item->only('id', 'post_id', 'user_id', 'text', 'created_by', 'created_at');
            })
            ->sortByDesc('created_at')
            ->sortByDesc('id')
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

        $response = $this->actingAs($users->first())->post(self::API_URL, [
            'post_id' => $post->id,
            'order_by' => 'created_at',
            'order' => 'desc'
        ]);

        $actual = $response->json();

        $this->assertNotNull($actual['next_cursor']);
        $this->assertNull($actual['prev_cursor']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => $total,
                'data' => $expected
            ]);
    }

    /**
     * 投稿IDで絞り込めるかを確認する
     *
     * @return void
     */
    public function test_can_view_data_by_post_id()
    {
        $users = User::factory(10)->create();
        Post::factory(15)->create();
        $comments = Comment::factory(15)->create();

        // 投稿idをランダムに抽出
        $post_id = $comments->random()->post->id;

        // ユーザーidで絞り込み
        $comments = $comments->where('post_id', $post_id);

        $total = $comments->count();
        $perPage = config('const.PER_PAGE.CURSOR_PAGINATE');
        $expected = $comments
            ->map(function($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;
                return $item->only('id', 'post_id', 'user_id', 'text', 'created_by', 'created_at');
            })
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

        $response = $this->actingAs($users->first())->post(self::API_URL, [
            'post_id' => $post_id
        ]);

        $actual = $response->json();

        $this->assertNull($actual['next_cursor']);
        $this->assertNull($actual['prev_cursor']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => $total,
                'data' => $expected
            ]);
    }
}
