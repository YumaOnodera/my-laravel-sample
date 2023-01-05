<?php

namespace Tests\Feature\Post;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/posts';

    /**
     * 期待値を生成する
     *
     * @param  Model|Collection  $posts
     * @param  Model|Collection  $users
     * @param  Model|Collection  $comments
     * @param  int  $perPage
     * @param  int  $page
     * @return array
     */
    private function expected(
        Model|Collection $posts,
        Model|Collection $users,
        Model|Collection $comments,
        int $perPage,
        int $page
    ): array {
        return $posts
            ->map(function ($item) use ($users, $comments) {
                $item['comments'] = $comments
                    ->where('post_id', $item->id)
                    ->map(function ($item) use ($users) {
                        $item['created_by'] = $users->find($item['user_id'])->name;

                        return $item;
                    })
                    ->values()
                    ->toArray();
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->sortBy([
                ['id', 'desc'],
            ])
            ->chunk($perPage)[$page - 1]
            ->values()
            ->toArray();
    }

    /**
     * 未ログインで実行した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_not_logged_in_can_view_data()
    {
        $users = User::factory(10)->create();
        $deletedUser = User::factory()->create([
            'deleted_at' => now(),
        ]);
        $posts = Post::factory(12)
            ->sequence(fn ($sequence) => [
                'user_id' => $sequence->index === 1 ? $deletedUser->id : $users->random()->id,
            ])
            ->create();
        $comments = Comment::factory(2)->create();

        // 削除ユーザーの投稿は除外する
        $posts = $posts->where('user_id', '<>', $deletedUser->id);

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $this->expected($posts, $users, $comments, $perPage, 1);

        $response = $this->get(self::API_URL);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => 1,
                'last_page' => $lastPage,
                'first_item' => 1,
                'last_item' => $perPage,
                'has_more_pages' => true,
                'data' => $expected,
            ]);
    }

    /**
     * レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_can_view_data()
    {
        $users = User::factory(10)->create();
        $deletedUser = User::factory()->create([
            'deleted_at' => now(),
        ]);
        $posts = Post::factory(12)
            ->sequence(fn ($sequence) => [
                'user_id' => $sequence->index === 1 ? $deletedUser->id : $users->random()->id,
            ])
            ->create();
        $comments = Comment::factory(2)->create();

        // 削除ユーザーの投稿は除外する
        $posts = $posts->where('user_id', '<>', $deletedUser->id);

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $this->expected($posts, $users, $comments, $perPage, 1);

        $response = $this->actingAs($users->first())->get(self::API_URL);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => 1,
                'last_page' => $lastPage,
                'first_item' => 1,
                'last_item' => $perPage,
                'has_more_pages' => true,
                'data' => $expected,
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
        $deletedUser = User::factory()->create([
            'deleted_at' => now(),
        ]);
        $posts = Post::factory(22)
            ->sequence(fn ($sequence) => [
                'user_id' => $sequence->index === 10 ? $deletedUser->id : $users->random()->id,
            ])
            ->create();
        $comments = Comment::factory(2)->create();

        // 削除ユーザーの投稿は除外する
        $posts = $posts->where('user_id', '<>', $deletedUser->id);

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $this->expected($posts, $users, $comments, $perPage, 2);

        $response = $this->actingAs($users->first())->get(self::API_URL.'?page=2');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => 2,
                'last_page' => $lastPage,
                'first_item' => $perPage + 1,
                'last_item' => $perPage * 2,
                'has_more_pages' => true,
                'data' => $expected,
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
        $deletedUser = User::factory()->create([
            'deleted_at' => now(),
        ]);
        $posts = Post::factory(12)
            ->sequence(fn ($sequence) => [
                'user_id' => $sequence->index === 1 ? $deletedUser->id : $users->random()->id,
            ])
            ->create();
        $comments = Comment::factory(2)->create();

        // 削除ユーザーの投稿は除外する
        $posts = $posts->where('user_id', '<>', $deletedUser->id);

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $firstItem = $perPage * ($lastPage - 1) + 1;
        $expected = $this->expected($posts, $users, $comments, $perPage, $lastPage);

        $response = $this->actingAs($users->first())->get(self::API_URL.'?page='.$lastPage);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $lastPage,
                'last_page' => $lastPage,
                'first_item' => $firstItem,
                'last_item' => $total,
                'has_more_pages' => false,
                'data' => $expected,
            ]);
    }

    /**
     * 1ページに表示する件数が変えられるかを確認する
     *
     * @return void
     */
    public function test_can_view_data_per_page()
    {
        $users = User::factory(10)->create();
        $deletedUser = User::factory()->create([
            'deleted_at' => now(),
        ]);
        $posts = Post::factory(17)
            ->sequence(fn ($sequence) => [
                'user_id' => $sequence->index === 1 ? $deletedUser->id : $users->random()->id,
            ])
            ->create();
        $comments = Comment::factory(2)->create();

        // 削除ユーザーの投稿は除外する
        $posts = $posts->where('user_id', '<>', $deletedUser->id);

        $total = $posts->count();
        $perPage = 15;
        $lastPage = ceil($total / $perPage);
        $expected = $this->expected($posts, $users, $comments, $perPage, 1);

        $response = $this->actingAs($users->first())->get(self::API_URL.'?per_page='.$perPage);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => 1,
                'last_page' => $lastPage,
                'first_item' => 1,
                'last_item' => $perPage,
                'has_more_pages' => true,
                'data' => $expected,
            ]);
    }

    /**
     * ユーザーIDで絞り込めるかを確認する
     *
     * @return void
     */
    public function test_can_view_data_by_user_id()
    {
        $users = User::factory(10)->create();
        $deletedUser = User::factory()->create([
            'deleted_at' => now(),
        ]);
        $posts = Post::factory(11)
            ->sequence(fn ($sequence) => [
                'user_id' => $sequence->index === 1 ? $deletedUser->id : $users->random()->id,
            ])
            ->create();
        $comments = Comment::factory(2)->create();

        // ユーザーidをランダムに5件抽出
        $user_ids = $users->random(5)->pluck('id')->toArray();

        // 削除ユーザーの投稿は除外し、ユーザーidで絞り込み
        $posts = $posts
            ->where('user_id', '<>', $deletedUser->id)
            ->whereIn('user_id', $user_ids);

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $this->expected($posts, $users, $comments, $perPage, 1);

        $query = '?user_ids[]='.implode('&user_ids[]=', $user_ids);
        $response = $this->actingAs($users->first())->get(self::API_URL.$query);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => 1,
                'last_page' => $lastPage,
                'first_item' => 1,
                'last_item' => $total,
                'has_more_pages' => false,
                'data' => $expected,
            ]);
    }
}
