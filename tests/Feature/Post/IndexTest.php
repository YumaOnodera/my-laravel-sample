<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
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
        $posts = Post::factory(11)->create();

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $posts
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

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
        $posts = Post::factory(11)->create();

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $posts
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

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
        $posts = Post::factory(21)->create();

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $posts
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->chunk($perPage)[1]
            ->values()
            ->toArray();

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
        $posts = Post::factory(11)->create();

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $firstItem = $perPage * ($lastPage - 1) + 1;
        $expected = $posts
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->chunk($perPage)[$lastPage - 1]
            ->values()
            ->toArray();

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
        $posts = Post::factory(16)->create();

        $total = $posts->count();
        $perPage = 15;
        $lastPage = ceil($total / $perPage);
        $expected = $posts
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

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
     * 作成日時の昇順で並び替えられるかを確認する
     *
     * @return void
     */
    public function test_can_view_data_with_sort_in_asc_order_of_created_at()
    {
        $users = User::factory(10)->create();
        $posts = Post::factory(11)->create();

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $posts
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->sortBy('created_at')
            ->sortBy('id')
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

        $response = $this->actingAs($users->first())->get(self::API_URL.'?order_by=created_at&order=asc');

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
     * 作成日時の降順で並び替えられるかを確認する
     *
     * @return void
     */
    public function test_can_view_data_with_sort_in_desc_order_of_created_at()
    {
        $users = User::factory(10)->create();
        $posts = Post::factory(11)->create();

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $posts
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->sortByDesc('created_at')
            ->sortByDesc('id')
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

        $response = $this->actingAs($users->first())->get(self::API_URL.'?order_by=created_at&order=desc');

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
        $posts = Post::factory(10)->create();

        // ユーザーidをランダムに5件抽出
        $user_ids = $users->random(5)->pluck('id')->toArray();

        // ユーザーidで絞り込み
        $posts = $posts->whereIn('user_id', $user_ids);

        $total = $posts->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $posts
            ->map(function ($item) use ($users) {
                $item['created_by'] = $users->find($item['user_id'])->name;

                return $item;
            })
            ->chunk($perPage)[0]
            ->values()
            ->toArray();

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
