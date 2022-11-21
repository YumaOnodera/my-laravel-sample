<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    private const API_URL = 'api/users';

    /**
     * 管理者ユーザーが実行した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_admin_user_can_view_data()
    {
        $users = User::factory(11)
            ->sequence(fn ($sequence) => [
                'is_admin' => $sequence->index === 0 ? 1 : 0,
            ])
            ->create();

        $total = $users->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $users->chunk($perPage)[0]->values()->toArray();

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
     * 一般ユーザーが実行した時、レスポンスが想定通りであることを確認する
     *
     * @return void
     */
    public function test_general_user_can_view_data()
    {
        $users = User::factory(11)->create();

        $total = $users->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $users
            ->map(function ($item) {
                return $item->only('id', 'name');
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
     * 管理者ユーザーが実行した時、論理削除されたデータを参照できることを確認する
     *
     * @return void
     */
    public function test_admin_user_can_view_soft_delete_data()
    {
        $users = User::factory(11)
            ->sequence(fn ($sequence) => [
                'is_admin' => $sequence->index === 0 ? 1 : 0,
            ])
            ->state(new Sequence(
                ['deleted_at' => null],
                ['deleted_at' => now()],
            ))
            ->create();

        $total = $users->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $users->chunk($perPage)[0]->values()->toArray();

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
     * 一般ユーザーが実行した時、論理削除されたデータを参照できないことを確認する
     *
     * @return void
     */
    public function test_general_user_can_not_view_soft_delete_data()
    {
        $users = User::factory(10)
            ->state(new Sequence(
                ['deleted_at' => null],
                ['deleted_at' => now()],
            ))
            ->create()
            ->whereNull('deleted_at');

        $total = $users->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $users
            ->map(function ($item) {
                return $item->only('id', 'name');
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
                'last_item' => $total,
                'has_more_pages' => false,
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
        $users = User::factory(21)
            ->sequence(fn ($sequence) => [
                'is_admin' => $sequence->index === 0 ? 1 : 0,
            ])
            ->create();

        $total = $users->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $expected = $users->chunk($perPage)[1]->values()->toArray();

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
        $users = User::factory(11)
            ->sequence(fn ($sequence) => [
                'is_admin' => $sequence->index === 0 ? 1 : 0,
            ])
            ->create();

        $total = $users->count();
        $perPage = config('const.PER_PAGE.PAGINATE');
        $lastPage = ceil($total / $perPage);
        $firstItem = $perPage * ($lastPage - 1) + 1;
        $expected = $users->chunk($perPage)[$lastPage - 1]->values()->toArray();

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
        $users = User::factory(16)
            ->sequence(fn ($sequence) => [
                'is_admin' => $sequence->index === 0 ? 1 : 0,
            ])
            ->create();

        $total = $users->count();
        $perPage = 15;
        $lastPage = ceil($total / $perPage);
        $expected = $users->chunk($perPage)[0]->values()->toArray();

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
}
